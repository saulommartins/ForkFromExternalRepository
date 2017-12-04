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
* Página processamento de Configuração de Controle de Pensão Alimenticia
* Data de: Criação   : 03/04/2006
# 20060419

* @author Analista: Vandré Miguel Ramos.
* @author Desenvolvedor: Bruce Cruz de Sena

* @ignore

* Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoPensaoFuncaoPadrao.class.php'                  );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoTipoEventoPensao.class.php'                    );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoPensaoEvento.class.php'                        );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoEvento.class.php'                              );

$stPrograma = 'ManterConfiguracaoPensao';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$obTFOLConfigPensao = new TFolhaPagamentoPensaoFuncaoPadrao;
$obTFOLPensaoEvento = new TFolhaPagamentoPensaoEvento;
$obTipoEvento       = new TFolhaPagamentoTipoEventoPensao   ;
$obEvento           = new TFolhaPagamentoEvento;

Sessao::getTransacao()->setMapeamento( $obTFOLConfigPensao );

$rsTipoEventos = new RecordSet;
$obTipoEvento->recuperaTodos( $rsTipoEventos );

$arFuncao = explode ( '.', $_POST['inCodFuncao'] );

$obTFOLConfigPensao->recuperaNow3($stNow, Sessao::getTransacao()) ;

$obTFOLConfigPensao->setDado( 'cod_configuracao_pensao' , 1            );
$obTFOLConfigPensao->setDado( 'timestamp'               , $stNow       );
$obTFOLConfigPensao->setDado( 'cod_funcao'              , $arFuncao[2] );
$obTFOLConfigPensao->setDado( 'cod_biblioteca'          , $arFuncao[1] );
$obTFOLConfigPensao->setDado( 'cod_modulo'              , $arFuncao[0] );
$obTFOLConfigPensao->inclusao();

while ( !$rsTipoEventos->eof() ) {

    $obTFOLPensaoEvento->obTFolhaPagamentoPensaoFuncaoPadrao = & $obTFOLConfigPensao;
    $obEvento->recuperaEventoCodigoNatureza( $rsEventos, $_POST['stInner_Cod_'.$rsTipoEventos->getCampo('cod_tipo')], 'D', true );
    $obTFOLPensaoEvento->setDado( 'cod_tipo'   , $rsTipoEventos->getCampo('cod_tipo') );
    $obTFOLPensaoEvento->setDado( 'cod_evento' , $rsEventos->getCampo('cod_evento')   );
    $obTFOLPensaoEvento->inclusao ();
    $rsTipoEventos->proximo();
}
    $stMensagem = "Configuração atualizada.";
    sistemaLegado::alertaAviso($pgForm,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
Sessao::encerraExcecao();

?>
