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
* Data de: Criação   : 14/09/2006
# 20060419

* @author Analista: Vandré Miguel Ramos.
* @author Desenvolvedor: Vandré Miguel Ramos.

* @ignore

* Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoTipoEventoDecimo.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoDecimoEvento.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';

$stPrograma = 'ManterConfiguracaoDecimo';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$obTAdministracaoConfiguracao         = new TAdministracaoConfiguracao();
$obTFolhaPagamentoDecimoEvento        = new TFolhaPagamentoDecimoEvento();
$obTFolhaPagamentoTipoEventoDecimo    = new TFolhaPagamentoTipoEventoDecimo();
$obTFolhaPagamentoEvento              = new TFolhaPagamentoEvento();

Sessao::getTransacao()->setMapeamento( $obTFolhaPagamentoDecimoEvento );

$rsTipoEventoDecimo = new RecordSet;
$obTFolhaPagamentoTipoEventoDecimo->recuperaTodos( $rsTipoEventoDecimo, $boTransacao );

$obTAdministracaoConfiguracao->setDado( "cod_modulo", "27");
$obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado( "parametro" , "mes_calculo_decimo".Sessao::getEntidade());
$obTAdministracaoConfiguracao->setDado( "valor"     , $request->get("inMesCalculoDecimo") );
$obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
$obTAdministracaoConfiguracao->alteracao($boTransacao);

//Inclusao do radio box Gera Adiant. de 13º Salário no mês de aniversário
$obTAdministracaoConfiguracao->setDado( "cod_modulo", "27");
$obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado( "parametro" , "adiantamento_13_salario".Sessao::getEntidade());
$obTAdministracaoConfiguracao->setDado( "valor"     , $request->get('boRdGerarAdiantamento13') );
$obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
$obTAdministracaoConfiguracao->alteracao($boTransacao);

//Caso entidade seja diferente de PREFEITURA
if ( Sessao::getEntidade() != '' ) {
    $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
    $obTAdministracaoConfiguracaoEntidade->setDado( 'exercicio'    , Sessao::getExercicio() );
    $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_entidade' , Sessao::getCodEntidade($boTransacao) );
    $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_modulo'   , '27');
    $obTAdministracaoConfiguracaoEntidade->setDado( 'parametro'    , 'adiantamento_13_salario'.Sessao::getEntidade());
    $obTAdministracaoConfiguracaoEntidade->setDado( 'valor'        , $request->get('boRdGerarAdiantamento13') );
    $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsConfiguracaoEntidade, $boTransacao );
    $obTAdministracaoConfiguracaoEntidade->alteracao($boTransacao);
}

while ( !$rsTipoEventoDecimo->eof() ) {
    $stFiltro = " WHERE codigo = '".$request->get('stInner_Cod_'.$rsTipoEventoDecimo->getCampo('cod_tipo'))."'";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro,"",$boTransacao);
    $obTFolhaPagamentoDecimoEvento->setDado( 'cod_tipo'   , $rsTipoEventoDecimo->getCampo('cod_tipo') );
    $obTFolhaPagamentoDecimoEvento->setDado( 'cod_evento' , $rsEvento->getCampo('cod_evento')   );
    $obTFolhaPagamentoDecimoEvento->inclusao($boTransacao);
    $rsTipoEventoDecimo->proximo();
}
$stMensagem = "Configuração atualizada.";
SistemaLegado::alertaAviso($pgForm,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
Sessao::encerraExcecao();

?>
