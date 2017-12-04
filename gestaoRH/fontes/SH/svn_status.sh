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
#!/usr/bin/env bash
# svn_status
#
# AUTORES    	: Rafael Garbin
# DESCRIÇÃO     : Script que faz a verificação de sujeira nos fontes a serem comitados
# USO        	: sh svn_status
# OBS           : Só funciona para arquivos modificados e adicionados
# LICENÇA    	: GPL v2
#

echo "Inicio da Analise dos arquivos a serem comitados(A,M)"
echo ""
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'sistemalegado::mostravar' {} | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '//'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH '>debug' {} | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '//'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'raise notice' {} | grep -v .svn | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'if ( params\["db_driver' {} | grep '\/\*' | grep -v svn | grep -v php | grep -v psql
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'if ( params\["db_driver' {} | grep '\#' | grep -v svn | grep -v php | grep -v psql
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH '\$preview->setDebug' {} | grep 'parcial' | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '//'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH '\$preview->setDebug' {} | grep 'completo' | grep -v .svn | grep -v .rptdesign | grep -v .agt | grep -v .sql | grep -v '//'

# Verificando inconsistencias do GRH, caso não tenha entidade nas PLS
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'folhapagamento\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'pessoal\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'ponto\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'estagio\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'concurso\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'beneficio\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'calendario\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'ima\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'diarias\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'
svn status ../../../ | egrep '^[MA]{1,1}[ ]{1,}' | tr " " "-" | cut -d "-" -f7 | xargs -i grep -riH 'beneficio\.' {} | grep -v svn | grep -v '\-\-' | grep -v .rptdesign | grep -v .php | grep -v .agt | grep -v '\-\-'

echo ""
echo "Analize concluida..."
