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
    * Página de Formulario de Encerramento/Reabertura de Mês Contábil
    * Data de Criação   : 06/11/2012

    * @author Analista:
    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Casos de uso: uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterEncerramentoMes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);

if ($boUtilizarEncerramentoMes == 'false') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois não está configurada!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    $arMeses   = array(
        1 => "Janeiro",
        2 => "Fevereiro",
        3 => "Mar&ccedil;o",
        4 => "Abril",
        5 => "Maio",
        6 => "Junho",
        7 => "Julho",
        8 => "Agosto",
        9 => "Setembro",
        10 => "Outubro",
        11 => "Novembro",
        12 => "Dezembro"
    );

    $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
    $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
    $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
    //busca meses encerrados
    $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

    //busca meses encerrados
    $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsMesesEncerrados, '', ' ORDER BY mes ');

    $arTmp = array();
    $arMesesEncerrados = array();
    if ($stAcao == 'encerrar') {
        foreach ($rsMesesEncerrados->arElementos as $mesEncerrado) {
            $arMesesEncerrados[$mesEncerrado['mes']] = $arMeses[$mesEncerrado['mes']];
        }

        $arTmp = array_diff_key($arMeses, $arMesesEncerrados);
    } else {
        foreach ($rsUltimoMesEncerrado->arElementos as $mes) {
            $arTmp[$mes['mes']] = $arMeses[$mes['mes']];
        }
    }

    //*****************************************************//
    // Define COMPONENTES DO FORMULARIO
    //*****************************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction( $pgProc );
    $obForm->setTarget( "oculto" );

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    //Define o objeto de controle
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( "" );
    // Monta combo dos meses para encerrar
    $obCmbMes = new Select;
    $obCmbMes->setRotulo('Mês de Encerramento');
    $obCmbMes->setId('inCodMes');
    $obCmbMes->setName('inCodMes');
    $obCmbMes->setStyle('width: 200px;');
    $obCmbMes->setNull(false);
    $obCmbMes->addOption( "", "Selecione" );
    foreach ($arTmp as $key => $value) {
        $obCmbMes->addOption( $key, $value );
    }

    $obBtnOk = new Ok();
    $obBtnOk->obEvento->setOnClick("confirmPopUp('".ucfirst($stAcao)." Mês', 'Tem certeza que deseja ".($stAcao)." o mês?', 'Salvar();', '');");

    $obBtnLimpar = new Limpar();

    //****************************************//
    // Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->setAjuda('UC-02.02.04');
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( ucfirst($stAcao)." Mês" );
    $obFormulario->addHidden( $obHdnCtrl );
    $obFormulario->addHidden( $obHdnAcao );
    $obFormulario->addComponente( $obCmbMes );

    $obFormulario->defineBarra(array($obBtnOk, $obBtnLimpar));
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
