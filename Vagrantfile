Vagrant.configure("2") do |config|
    config.vm.provider :virtualbox do |v|
        v.name = "escqrs1.box"
        v.customize [
            "modifyvm", :id,
            "--name", "escqrs1.box",
            "--memory", 1024,
            "--cpus", 1,
        ]
    end

    config.vm.box = "ubuntu/trusty64"
    config.vm.hostname = "escqrs1.box"
    config.vm.network :private_network, ip: "192.168.16.16"
    config.ssh.forward_agent = true

    config.vm.synced_folder "./", "/vagrant", type: "nfs"
end