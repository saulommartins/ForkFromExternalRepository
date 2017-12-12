/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
create or replace function stn.tituloRCL( varchar ) returns  varchar as '

 select cast( case 
                    when $1 =  ''4.1.1.1.2.02''    then ''IPTU''
                    when $1 =  ''4.1.1.1.2.04''    then ''ISS''
                    when $1 =  ''4.1.1.1.2.08''    then ''ITBI''
                    when $1 =  ''4.1.7.2.1.01.02'' then ''Cota-Parte do FPM''
                    when $1 =  ''4.9.7'' then ''Dedução da Receita para Formação do FUNDEB'' 
                    
                    when $1 =  ''4.1.7.2.1.36''	   then ''Transferencia da LC 87/1996''
                    when $1 =  ''4.1.7.2.1.01.05'' then ''Cota-Parte do ITR''
                    when $1 =  ''4.1.2.2.0.03''    then ''Contribuições Economicas''
                    when $1 =  ''4.1.2.1.0.99.00.10'' then ''Comp. Prev. Regime Previdencia''
                    when $1 =  ''4.1.7.2.4''       then ''Transferèncias De Recursos Do FUNDEB''
                    when $1 =  ''9.1.7.2.4''       then ''Transferèncias De Recursos Do FUNDEB''
                    
                    else null
                    end as varchar 
            )    
           as titulo;

'language 'sql';
