// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import {Vm} from "forge-std/Vm.sol";
import {console} from "forge-std/console.sol";

contract Fork {
    Vm private vm = Vm(address(uint160(uint256(keccak256("hevm cheat code")))));

    mapping(uint32 chainId => string envString) private forkAliases;

    mapping(uint32 chainId => ForkData) private _forkId;

    mapping(uint32 chainId => uint256) internal defaultForkBlockNumber;

    // Created because forkId can be 0 and cannot be used to prove fork existence
    struct ForkData {
        uint256 forkId;
        bool exists;
    }

    constructor() {
        _configureForkAlias();
    }

    function fork(uint32 chainId) public {
        ForkData memory forkData = _forkId[chainId];
        uint256 forkId = forkData.forkId;

        if (forkData.exists) {
            bool isForkActive = vm.activeFork() == forkData.forkId;

            if (isForkActive) return;

            vm.selectFork(forkId);
            return;
        }

        // Create new fork
        string memory forkAlias = forkAliases[chainId];
        uint256 blockNumber = defaultForkBlockNumber[chainId];

        if (blockNumber != 0) forkId = vm.createFork(forkAlias, blockNumber);
        else forkId = vm.createFork(forkAlias);

        _forkId[chainId] = ForkData(forkId, true);

        vm.selectFork(forkId);
    }

    function isForkAvailable(uint32 chainId) public view returns (bool) {
        bytes32 _fork = keccak256(abi.encode(forkAliases[chainId]));
        bytes32 emptyString = keccak256(abi.encode(""));
        return _fork != emptyString;
    }

    function _configureForkAlias() internal virtual {
        forkAliases[1] = "mainnet";
        forkAliases[10] = "optimism";
        forkAliases[56] = "bsc";
        forkAliases[137] = "polygon";
        forkAliases[1088] = "metis";
        forkAliases[5000] = "mantle";
        forkAliases[8453] = "base";
        forkAliases[42161] = "arbitrum";
        forkAliases[43114] = "avalanche";
        forkAliases[1329] = "sei";
    }
}
