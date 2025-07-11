// SPDX-License-Identifier: MIT
pragma solidity ^0.8.27;

import {Script} from "forge-std/Script.sol";
import {CharityPlatformTest} from "../../src/CharityPlatformTest.sol";
import {console} from "forge-std/console.sol";

contract CharityPlatformTestDeploy is Script {
    uint256 privateKey = vm.envUint("PRIVATE_KEY");
    address owner = 0xB98BC23f1EdDb754d01DBc7B62B28039eC9A0cD9;

    address feeReceiver = owner;

    string orgName1 = "Lighthouse Charity";
    string orgName2 = "Green Tomorrow Foundation";
    string orgName3 = "Little Miracles";
    string orgName4 = "Global Kindness Network";

    // string orgName5 = "Compassion Collective"

    address wallet1 = 0x889A54728511DAC5f5399584Fd014Ef57e634894;
    address wallet2 = 0x2F1EE9B5CC1464Bb49a67c52C387493d79256230;
    address wallet3 = 0x700114A467b43B094C84196A9FdCb4dfA90543ec;
    address wallet4 = 0x4223E67a1DFdB6A2D0C299Ba5EA03a57d5865Be6;

    // address wallet5 =

    function run() public {
        vm.createSelectFork(vm.envString("ARBITRUM_MAINNET_RPC_URL"));
        _deploy();
    }

    function _deploy() internal {
        vm.startBroadcast(privateKey);

        CharityPlatformTest charityPlatform = new CharityPlatformTest(owner, feeReceiver);

        console.log("CharityPlatform deployed on address: ", address(charityPlatform));
        console.log("owner: ", owner);
        console.log("feeReceiver: ", feeReceiver);

        charityPlatform.registerOrganization(orgName1, wallet1);
        charityPlatform.registerOrganization(orgName2, wallet2);
        charityPlatform.registerOrganization(orgName3, wallet3);
        charityPlatform.registerOrganization(orgName4, wallet4);

        console.log("1) organization registered with: ", orgName1, wallet1);
        console.log("2) organization registered with:", orgName2, wallet2);
        console.log("3) organization registered with: ", orgName3, wallet3);
        console.log("4) organization registered with:", orgName4, wallet4);

        vm.stopBroadcast();
    }
}
