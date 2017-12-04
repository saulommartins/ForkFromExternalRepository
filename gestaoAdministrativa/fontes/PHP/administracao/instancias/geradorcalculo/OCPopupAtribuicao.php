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
    * Arquivo de instância para manutenção de funções
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: OCPopupAtribuicao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_ADM_NEGOCIO . "RFuncao.class.php"  );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RFuncao;
// Acoes por pagina
switch ($stCtrl) {

    case "MontaCondicao":

        $arCondicao = Sessao::read('Condicao');
        if(substr($_GET['stSelecionado'],0,1)=='-' and count($arCondicao) > 0)
            $_GET['stSelecionado'] =  $_GET['stSelecionado'] . ' ';

        $arCondicao[] = str_replace("-","#",$_GET['stSelecionado']);

        Sessao::write('Condicao', $arCondicao);

        $stHtml = implode("",$arCondicao);
        $stHtml = str_replace(' ','&nbsp;',$stHtml);
        $stHtml = str_replace("'","\'",$stHtml);
        $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
        $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";
    break;
    case "MontaCondicaoBotoes":

        $arCondicao = Sessao::read('Condicao');

        if ($_GET['stSelecionado'] != 'DEL') {
            if($_GET['stSelecionado'] == 'Soma')
                $_GET['stSelecionado'] = '+';

            $arCondicao[] = $_GET['stSelecionado'];
            Sessao::write('Condicao', $arCondicao);
        } else {
            for ($inCount=0; $inCount< (count($arCondicao)-1); $inCount++) {
                $arTmp[$inCount] = $arCondicao[$inCount];
            }

            if (is_array($arTmp)) {
                $arCondicao = $arTmp;
                Sessao::write('Condicao', $arCondicao);
            }
        }

        $stHtml = implode("",$arCondicao);
        $stHtml = str_replace(' ','&nbsp;',$stHtml);
        $stHtml = str_replace("'","\'",$stHtml);
        $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
        $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";
    break;
    case "MontaParametrosFuncao":
        if ($_POST['stFuncao']) {
            $rsParametros = $rsFuncao = new RecordSet;
            $obFormulario = new Formulario;

            $obRegra->setNomeFuncao( $_POST['stFuncao'] );
            $obRegra->listar( $rsFuncao );

            $obRegra->obRVariavel->setCodFuncao( $rsFuncao->getCampo('cod_funcao') );
            $obRegra->obRVariavel->setParametro( true );
            $obRegra->obRVariavel->listar( $rsParametros );

            while ( !$rsParametros->eof() ) {
                $stTipoVariavel = $rsParametros->getCampo('nom_tipo');

                if ($stTipoVariavel=="BOOLEANO") {
                    $obRdbVerdadeiro = new Radio;
                    $obRdbVerdadeiro->setRotulo ( $rsParametros->getCampo('nom_variavel') );
                    $obRdbVerdadeiro->setLabel  ("Verdadeiro");
                    $obRdbVerdadeiro->setName   ( $rsParametros->getCampo('nom_variavel') );
                    $obRdbVerdadeiro->setValue  ("VERDADEIRO");
                    $obRdbVerdadeiro->setChecked(false);
                    $obRdbFalso = new Radio;
                    $obRdbFalso->setRotulo ( $rsParametros->getCampo('nom_variavel') );
                    $obRdbFalso->setLabel  ("Falso");
                    $obRdbFalso->setName   ( $rsParametros->getCampo('nom_variavel') );
                    $obRdbFalso->setValue  ("FALSO");
                    $obRdbFalso->setChecked(false);
                    $obFormulario->addComponenteComposto( $obRdbVerdadeiro , $obRdbFalso );
                } elseif ($stTipoVariavel=="DATA") {
                    $obTxtParametro = new Data;
                } else {
                    $obTxtParametro = new TextBox;
                    $obTxtParametro->setRotulo        ( $rsParametros->getCampo('nom_variavel') );
                    $obTxtParametro->setName          ( $rsParametros->getCampo('nom_variavel') );
                    $obTxtParametro->setSize          ( 30 );
                    $obTxtParametro->setMaxLength     ( 200 );
                    $obTxtParametro->setNull          ( false );
                    $obTxtParametro->obEvento->setOnChange("document.frm.stVariavel.options[0].selected=true;");
                    if($stTipoVariavel=="INTEIRO")
                        $obTxtParametro->setInteiro   ( true );
                    elseif($stTipoVariavel=="NUMERICO")
                        $obTxtParametro->obEvento->setOnKeyPress("return tfloatPonto(this, event);");
                    $obFormulario->addComponente( $obTxtParametro );
                }
                $rsParametros->proximo();
            }

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);

            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $js  = "d.getElementById('spnFuncao').innerHTML = '".$stHtml."';";
            $js .= "f.stFuncaoEval.value  = '".$stEval."';";
        }
    break;
    case "MontaFuncao":
        $rsParametros = $rsFuncao = new RecordSet;
        $obFormulario = new Formulario;

        $arFuncoes = Sessao::read('Funcoes');
        $arFuncoes[] = $_POST['stFuncao'];

        Sessao::write('Funcoes', $arFuncoes);

        $obRegra->setNomeFuncao( $_POST['stFuncao'] );
        $obRegra->listar( $rsFuncao );

        $obRegra->obRVariavel->setCodFuncao( $rsFuncao->getCampo('cod_funcao') );
        $obRegra->obRVariavel->setParametro( true );
        $obRegra->obRVariavel->listar( $rsParametros );

        $_GET['stFuncao'] .= '(';
        while ( !$rsParametros->eof() ) {
            $stTipoVariavel = $rsParametros->getCampo('nom_tipo');
            $stNomeVariavel = $rsParametros->getCampo('nom_variavel');
            if ($stTipoVariavel=="TEXTO") {
                $$stNomeVariavel = "''".$$stNomeVariavel."''";
            }
            $_GET['stFuncao'] .= $$stNomeVariavel;
            $rsParametros->proximo();
        }
        $_GET['stFuncao'] .= ')';

        $arCondicao = Sessao::read('Condicao');
        $arCondicao[] = ' '.$_GET['stFuncao'];

        Sessao::write('Condicao', $arCondicao);

        $stHtml = implode(" ",$arCondicao);
        $stHtml = str_replace(' ','&nbsp;',$stHtml);
        $stHtml = str_replace("'","\'",$stHtml);
        $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
        $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";
    break;
    case "Fechar":
        $js = "parent.window.close();";

    break;
}
if($js)
    SistemaLegado::executaIFrameOculto($js);
?>
