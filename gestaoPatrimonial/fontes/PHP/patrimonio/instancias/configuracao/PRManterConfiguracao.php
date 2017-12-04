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
    * Data de Criação: 04/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.01.01

    $Id: PRManterConfiguracao.php 60499 2014-10-23 20:20:33Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

$stPrograma = "ManterConfiguracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTAdministracaoConfiguracao );

switch ($stAcao) {
    case 'manter':
        $codContaSintetica = ($_REQUEST['inCodContaSintetica'] != '') ? $_REQUEST['inCodContaSintetica'] : $_REQUEST['hdnCodContaSintetica'];
        
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
        $obTAdministracaoConfiguracao->setDado( 'parametro','grupo_contas_permanente' );
        $obTAdministracaoConfiguracao->setDado( 'valor', $codContaSintetica );
        $obTAdministracaoConfiguracao->recuperaPorChave($rsRecordSet);
        
        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $obTAdministracaoConfiguracao->alteracao();
        } else {
            $obTAdministracaoConfiguracao->inclusao();
        }
        
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
        $obTAdministracaoConfiguracao->setDado( 'parametro','texto_ficha_transferencia' );
        $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stTransferencia'] );
        $obTAdministracaoConfiguracao->alteracao();
        
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
        $obTAdministracaoConfiguracao->setDado( 'parametro','alterar_bens_exercicio_anterior' );
        $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['boAlterarBem'] );
        $obTAdministracaoConfiguracao->alteracao();
        
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
        $obTAdministracaoConfiguracao->setDado( 'parametro','placa_alfanumerica' );
        $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['boPlacaAlfa'] );
        $obTAdministracaoConfiguracao->alteracao();
        
        $stDigitosLocal = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='coletora_digitos_local'");
        $stDigitosPlaca = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='coletora_digitos_placa'");
        $stCaracterSeparador = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='coletora_separador'");
        
        if ($stDigitosLocal != '') {
            $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
            $obTAdministracaoConfiguracao->setDado( 'parametro','coletora_digitos_local' );
            $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stColetoraDigitosLocal'] );
            $obTAdministracaoConfiguracao->alteracao();
        } else {
            $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
            $obTAdministracaoConfiguracao->setDado( 'parametro','coletora_digitos_local' );
            $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stColetoraDigitosLocal'] );
            $obTAdministracaoConfiguracao->inclusao();
        }

        if ($stDigitosPlaca != '') {
            $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
            $obTAdministracaoConfiguracao->setDado( 'parametro','coletora_digitos_placa' );
            $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stColetoraDigitosPlaca'] );
            $obTAdministracaoConfiguracao->alteracao();
        } else {
            $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
            $obTAdministracaoConfiguracao->setDado( 'parametro','coletora_digitos_placa' );
            $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stColetoraDigitosPlaca'] );
            $obTAdministracaoConfiguracao->inclusao();
        }

        if ($stCaracterSeparador != '') {
            $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
            $obTAdministracaoConfiguracao->setDado( 'parametro','coletora_separador' );
            $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stColetoraCaracterSeparador'] );
            $obTAdministracaoConfiguracao->alteracao();
        } else {
            //valida se o parametro vem vazio do banco e se o usuario setou como vazio o campo de Caracter Separador
            if ( ($_REQUEST['stColetoraCaracterSeparador'] == '') &&  ($stCaracterSeparador == $_REQUEST['stColetoraCaracterSeparador']) ) {
                $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
                $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
                $obTAdministracaoConfiguracao->setDado( 'parametro','coletora_separador' );
                $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stColetoraCaracterSeparador'] );
                $obTAdministracaoConfiguracao->alteracao();
            } else {
                $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
                $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
                $obTAdministracaoConfiguracao->setDado( 'parametro','coletora_separador' );
                $obTAdministracaoConfiguracao->setDado( 'valor', $_REQUEST['stColetoraCaracterSeparador'] );
                $obTAdministracaoConfiguracao->inclusao();
            }

        }

        $flValorMinimoDeprecicao = str_replace('.', '', $_REQUEST['flValorMinimoDepreciacao'] );
        $flValorMinimoDeprecicao = str_replace(',', '.', $flValorMinimoDeprecicao );

        # Depreciação
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
        $obTAdministracaoConfiguracao->setDado( 'parametro','valor_minimo_depreciacao' );
        $obTAdministracaoConfiguracao->setDado( 'valor', $flValorMinimoDeprecicao );

        $testaParametro = SistemaLegado::pegaDado( 'valor', 'administracao.configuracao', " WHERE parametro = 'valor_minimo_depreciacao' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 ");

        if ($testaParametro == null) {
            $obTAdministracaoConfiguracao->inclusao();
        } else {
            $obTAdministracaoConfiguracao->alteracao();
        }
        
        // A depreciação automática deverá ser sempre mensal, por isso valor fixo de 1
        $obTAdministracaoConfiguracao->setDado('exercicio'  , Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado('cod_modulo' , 6 );
        $obTAdministracaoConfiguracao->setDado('parametro'  , 'competencia_depreciacao' );
        $obTAdministracaoConfiguracao->setDado('valor'      , 1 );

        $testaParametro = SistemaLegado::pegaDado( 'valor', 'administracao.configuracao', " WHERE parametro = 'competencia_depreciacao' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 ");

        if ($testaParametro == null) {
            $obTAdministracaoConfiguracao->inclusao();
        } else {
            $obTAdministracaoConfiguracao->alteracao();
        }
        
        // Não utilizará mais substituição, terá que anular a depreciação automática da competência
        $obTAdministracaoConfiguracao->setDado( 'exercicio' , Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
        $obTAdministracaoConfiguracao->setDado( 'parametro' ,'substituir_depreciacao' );
        $obTAdministracaoConfiguracao->setDado( 'valor'     , "false" );

        $testaParametro = SistemaLegado::pegaDado( 'valor', 'administracao.configuracao', " WHERE parametro = 'substituir_depreciacao' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 ");

        if (isset($testaParametro)) {
            $obTAdministracaoConfiguracao->alteracao();
        } else {
            $obTAdministracaoConfiguracao->inclusao();
        }

        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração","alterar","aviso", Sessao::getId(), "../");
    break;
}

Sessao::encerraExcecao();

?>
