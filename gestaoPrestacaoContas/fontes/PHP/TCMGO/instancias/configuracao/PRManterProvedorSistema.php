<?php
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
/*
 * Processamento de Provedor do Sistema
 * Data de Criação: 27/01/2015

 * @author Desenvolvedor Evandro Melos

 * @package URBEM
 * @subpackage

 * @ignore

 * $Id: $
 
 */
 
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/mapeamento/TAdministracaoConfiguracao.class.php';

$stPrograma = "ManterProvedorSistema";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();

//Inicia o controle de transação
Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTAdministracaoConfiguracao );

$obTAdministracaoConfiguracao->setDado('exercicio'  , Sessao::getExercicio()    );
$obTAdministracaoConfiguracao->setDado('cod_modulo' , 42                        );
$obTAdministracaoConfiguracao->setDado('parametro'  , 'provedor_sistema'        );
$obTAdministracaoConfiguracao->setDado('valor'      , $request->get('inCodCGM') );
$obTAdministracaoConfiguracao->alteracao();

//Encerra o controle de transação
Sessao::encerraExcecao();

SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");

?>