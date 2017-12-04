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
?>
<?php
/**
    * Página de Processamento
    * Data de Criação   : 10/05/2007

    * @author Henrique Boaventura

    * @ignore

    *$Id: PRManterPfdaaaa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO.'TTGOBalancoPfdaaaa.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterPfdaaaa";
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
        $obTTGOBalancoPfdaaaa = new TTGOBalancoPfdaaaa();
        $obTTGOBalancoPfdaaaa->setDado( 'exercicio', Sessao::getExercicio() );
        if ( count( $arExcluidas ) > 0 ) {
            foreach ($arExcluidas as $arAux) {
                foreach ($arAux as $arContas) {
                    $obTTGOBalancoPfdaaaa->setDado('cod_plano',$arContas['cod_plano']);
                    $obTTGOBalancoPfdaaaa->exclusao();
                }
            }
        }
        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                if ( count( $arAux ) > 0 ) {
                    foreach ($arAux as $arContas) {
                        $obTTGOBalancoPfdaaaa->setDado( 'cod_plano', $arContas['cod_plano'] );
                        $obTTGOBalancoPfdaaaa->setDado( 'tipo_lancamento', $arContas['tipo_lancamento'] );
                        $obTTGOBalancoPfdaaaa->setDado( 'desdobramento_tipo', $arContas['desdobramento_tipo'] );
                        $obTTGOBalancoPfdaaaa->recuperaRelacionamento( $rsContas );
                        if ( $rsContas->getNumLinhas() <= 0 ) {
                            $obTTGOBalancoPfdaaaa->inclusao();
                        }
                    }
                }
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}

Sessao::encerraExcecao();
