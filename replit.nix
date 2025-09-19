{ pkgs }: {
  deps = [
    pkgs.sqlite
    pkgs.bashInteractive
    pkgs.nodePackages.bash-language-server
    pkgs.man
  ];
}