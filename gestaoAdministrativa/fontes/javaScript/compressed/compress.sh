#/*
#    **********************************************************************************
#    *                                                                                *
#    * @package URBEM CNM - Soluções em Gestão Pública                                *
#    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
#    * @author Confederação Nacional de Municípios                                    *
#    *                                                                                *
#    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
#    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
#    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
#    *                                                                                *
#    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
#    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
#    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
#    * para mais detalhes.                                                            *
#    *                                                                                *
#    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
#    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
#    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
#    *                                                                                *
#    **********************************************************************************
#*/
#!/bin/bash

#
# gzip and compress
# Lucas Stephanou
#
echo -e "Minificando"
# comprimir
java -jar yuicompressor-2.3.6.jar --nomunge ../ajax.js                        > /tmp/ajax.js      
java -jar yuicompressor-2.3.6.jar --nomunge ../arvore.js                      > /tmp/arvore.js    
java -jar yuicompressor-2.3.6.jar --nomunge ../funcoesJs.js                   > /tmp/funcoesJs.js 
java -jar yuicompressor-2.3.6.jar --nomunge ../genericas.js                   > /tmp/genericas.js 
java -jar yuicompressor-2.3.6.jar --nomunge ../ifuncoesJs.js                  > /tmp/ifuncoesJs.js
java -jar yuicompressor-2.3.6.jar --nomunge ../login.js                       > /tmp/login.js     
java -jar yuicompressor-2.3.6.jar --nomunge ../mascaras.js                    > /tmp/mascaras.js  
java -jar yuicompressor-2.3.6.jar --nomunge ../prototype.js                   > /tmp/prototype.js 
java -jar yuicompressor-2.3.6.jar --nomunge ../qTip.js                        > /tmp/qTip.js      
java -jar yuicompressor-2.3.6.jar --nomunge ../table_tree.js                  > /tmp/table_tree.js
java -jar yuicompressor-2.3.6.jar --nomunge ../tipo.js                        > /tmp/tipo.js      
java -jar yuicompressor-2.3.6.jar --nomunge ../Window.js                      > /tmp/Window.js    
java -jar yuicompressor-2.3.6.jar --nomunge ../jquery.js                      > /tmp/jquery.js
java -jar yuicompressor-2.3.6.jar --nomunge ../jquery.selectboxes.js          > /tmp/jquery.selectboxes.js
java -jar yuicompressor-2.3.6.jar --nomunge ../jquery-ui.js                   > /tmp/jquery-ui.js
java -jar yuicompressor-2.3.6.jar --nomunge ../jquery.price_format.1.2.js     > /tmp/jquery.price_format.1.2.js
java -jar yuicompressor-2.3.6.jar --nomunge ../jquery.meiomask.js             > /tmp/jquery.meiomask.js

mv /tmp/ajax.js                         ajax.js
mv /tmp/arvore.js                       arvore.js
mv /tmp/funcoesJs.js                    funcoesJs.js
mv /tmp/genericas.js                    genericas.js
mv /tmp/ifuncoesJs.js                   ifuncoesJs.js
mv /tmp/login.js                        login.js
mv /tmp/mascaras.js                     mascaras.js
mv /tmp/prototype.js                    prototype.js
mv /tmp/qTip.js                         qTip.js
mv /tmp/table_tree.js                   table_tree.js
mv /tmp/tipo.js                         tipo.js
mv /tmp/Window.js                       Window.js
mv /tmp/jquery.js                       jquery.js
mv /tmp/jquery.selectboxes.js           jquery.selectboxes.js
mv /tmp/jquery-ui.js                    jquery-ui.js
mv /tmp/jquery.price_format.1.2.js      jquery.price_format.1.2.js
mv /tmp/jquery.meiomask.js              jquery.meiomask.js

#echo -e "Comprimindo"
#gzip
#gzip file.js -c > file.js
#gzip < /tmp/ajax.js        -c >  ajax.js
#gzip < /tmp/arvore.js      -c >  arvore.js
#gzip < /tmp/funcoesJs.js   -c >  funcoesJs.js
#gzip < /tmp/genericas.js   -c >  genericas.js
#gzip < /tmp/ifuncoesJs.js  -c >  ifuncoesJs.js
#gzip < /tmp/login.js       -c >  login.js
#gzip < /tmp/mascaras.js    -c >  mascaras.js
#gzip < /tmp/prototype.js   -c >  prototype.js
#gzip < /tmp/qTip.js        -c >  qTip.js
#gzip < /tmp/urbem.js     -c >  urbem.js
#gzip < /tmp/table_tree.js  -c >  table_tree.js
#gzip < /tmp/tipo.js        -c >  tipo.js
#gzip < /tmp/Window.js      -c >  Window.js
