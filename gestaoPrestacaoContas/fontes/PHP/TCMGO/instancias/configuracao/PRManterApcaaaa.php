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

    * Página de Processamento
    * Data de Criação   : 10/05/2007

    * @author Henrique Boaventura

    * @ignore

    *$Id: PRManterApcaaaa.php 61679 2015-02-25 13:07:38Z evandro $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO.'TTGOBalancoApcaaaa.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterApcaaaa";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$stAcao = $request->get('stAcao');

$arContas = Sessao::read('arContas');
$arExcluidas = Sessao::read('arExcluidas');

switch ($_REQUEST['stAcao']) {
    case 'configurar' :
        $obTTGOBalancoApcaaaa = new TTGOBalancoApcaaaa();
        $obTTGOBalancoApcaaaa->setDado( 'exercicio', Sessao::getExercicio() );      

        //excluir contas
        if ( count( $arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento']] ) > 0 ) {
            foreach ( $arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento']] as $arAux ) {                                
                $obTTGOBalancoApcaaaa->setDado('cod_plano',$arAux['cod_plano']);
                $obTTGOBalancoApcaaaa->exclusao( $boTransacao );
            }
        }

        //Adicionar ou atualizar contas de acordo com o tipo de lancamento
        if ( count( $arContas['arContas_'.$_REQUEST['inTipoLancamento']] ) > 0 ) {                         
            foreach ($arContas['arContas_'.$_REQUEST['inTipoLancamento']] as $arAux) {
                $obTTGOBalancoApcaaaa->setDado( 'cod_plano', $arAux['cod_plano'] );
                $obTTGOBalancoApcaaaa->setDado( 'tipo_lancamento', $arAux['tipo_lancamento'] );
                $obTTGOBalancoApcaaaa->recuperaRelacionamento( $rsContas );
                       
                if ( $rsContas->getNumLinhas() > 0 ) {
                    $obTTGOBalancoApcaaaa->alteracao( $boTransacao );
                }  else {
                    $obTTGOBalancoApcaaaa->inclusao( $boTransacao );
                }                
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}

Sessao::encerraExcecao();

?>