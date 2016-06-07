#!/bin/bash

tar -xvzf gromacs-5.0.7.tar.gz
mv gromacs-5.0.7 gmx507
mkdir gmx507/build
cd gmx507

cmake ./ \
   -DGMX_BUILD_OWN_FFTW=ON \
   -DCMAKE_INSTALL_PREFIX=/home/jmcrowet/Software/gmx507/build/ \
   -DBUILD_SHARED_LIBS=off \
   -DGMX_FFT_LIBRARY=fftw3

