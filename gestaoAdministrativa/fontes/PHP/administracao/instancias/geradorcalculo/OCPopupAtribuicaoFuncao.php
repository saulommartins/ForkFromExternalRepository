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

    $Id: OCPopupAtribuicaoFuncao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.03.95

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_ADM_NEGOCIO . "RFuncao.class.php"  );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra    = new RFuncao;
// Acoes por pagina
switch ($stCtrl) {
    case "MontaParametrosFuncao":
        if ($_POST['stFuncao']) {
            $rsParametros = $rsFuncao = new RecordSet;
            $obFormulario = new Formulario;

            $obRegra->setNomeFuncao( $_POST['stFuncao'] );
            $obRegra->listar( $rsFuncao );
            $obRegra->obRVariavel->setCodFuncao( $rsFuncao->getCampo('cod_funcao') );
            $obRegra->obRVariavel->setCodModulo( $rsFuncao->getCampo('cod_modulo') );
            $obRegra->obRVariavel->setCodBiblioteca( $rsFuncao->getCampo('cod_biblioteca') );
            $obRegra->obRVariavel->setParametro( true );
            $obRegra->obRVariavel->listar( $rsParametros );

            $arParametros   = Sessao::read('arParametros');
            $VariaveisTipo  = Sessao::read('VariaveisTipo');
            $ParametrosTipo = Sessao::read('ParametrosTipo');

            $prCount = 0;
            while ( !$rsParametros->eof() ) {
                $rsVariavel     = new RecordSet;
                $arVariaveis    = array();
                $stTipoVariavel = $rsParametros->getCampo('nom_tipo');
                $stNomVariavel  = $rsParametros->getCampo('nom_variavel');
                $stValor        = 'stValor_'    . $stNomVariavel;
                $stVariavel     = 'stVariavel_' . $stNomVariavel;
                $stParametro    = 'stParametro_'. $_POST['stFuncao'].'_'.$rsParametros->getCorrente();
                $$stParametro   = $_POST[ $stParametro ];
                if ($$stParametro == '') {
                      $$stParametro = $arParametros[$prCount];
                }
                if ( (substr($$stParametro,0,1)=='#') && ( strtoupper(substr($$stParametro,1,1))>='A' && strtoupper(substr($$stParametro,1,1))<='Z' )) {
                    $$stValor   = '';
                    $$stVariavel= '-'.substr($$stParametro,1,strlen($$stParametro));
                } else {
                    $$stValor   = $$stParametro;
                    $$stVariavel= '';
                }
                //Caso não tenha variável nem valor selecionado/indicado, seta variável com o mesmo nome do parâmetro
                if ((!$$stVariavel) && (strlen($$stValor)==0)) {
                    $$stVariavel = '-'.$stNomVariavel;
                }
                //Variaveis
                for ($inCount=0; $inCount<count($VariaveisTipo); $inCount++) {
                    if ($VariaveisTipo[$inCount]['stTipoVariavel']==$stTipoVariavel) {
                        $arVariaveis[] = $VariaveisTipo[$inCount];
                    }
                }
                //Parametros
                for ($inCount=0; $inCount<count($ParametrosTipo); $inCount++) {
                    $arTmp['stTipoVariavel'] = $ParametrosTipo[$inCount]['stTipoParametro'];
                    $arTmp['stNomeVariavel'] = $ParametrosTipo[$inCount]['stNomeParametro'];
                    if ($arTmp['stTipoVariavel']==$stTipoVariavel) {
                        $arVariaveis[] = $arTmp;
                    }
                }
                //Preenche o RecordSet com o array de variáveis de um determinado tipo
                $rsVariavel->preenche( $arVariaveis );

                if ($stTipoVariavel=="DATA") {
                  if ($$stValor != '') {
                    $data = explode("-",$$stValor);
                    $$stValor = $data[2]."/".$data[1]."/".$data[0];
                  }
                    $obTxtValor = new Data;
                } else {
                $obTxtValor = new TextBox;
                }
                $obTxtValor->setRotulo        ( $stNomVariavel );
                $obTxtValor->setName          ( $stValor  );
                $obTxtValor->setValue         ( $$stValor );
                $obTxtValor->setSize          ( 25 );
                $obTxtValor->setMaxLength     ( 200 );
                $obTxtValor->setNull          ( false );
                $obTxtValor->obEvento->setOnChange("document.frm.$stVariavel.options[0].selected=true;");
                if($stTipoVariavel=="INTEIRO")
                    $obTxtValor->setInteiro   ( true );
                elseif ($stTipoVariavel=="NUMERICO") {
                    $obTxtValor->setMaxLength(14);
                    $obTxtValor->obEvento->setOnKeyPress("return tfloatPonto(this, event);");
                }

                $obCmbVariavel = new Select;
                $obCmbVariavel->setRotulo        ( $stNomVariavel );
                $obCmbVariavel->setName          ( $stVariavel );
                $obCmbVariavel->setStyle         ( "width: 200px");
                $obCmbVariavel->setCampoID       ( "-[stNomeVariavel]" );
                $obCmbVariavel->setCampoDesc     ( "#[stNomeVariavel]" );
                $obCmbVariavel->addOption        ( "", "Selecione" );
                if ($stTipoVariavel=="BOOLEANO") {
                    $obCmbVariavel->addOption        ( "VERDADEIRO", "VERDADEIRO" );
                    $obCmbVariavel->addOption        ( "FALSO"     , "FALSO" );
                }
                if ($stTipoVariavel=="TEXTO") {
                     $obCmbVariavel->addOption        ( "VAZIO", "VAZIO" );
                }
                $obCmbVariavel->setValue         ( $$stVariavel );
                $obCmbVariavel->setNull          ( false );
                $obCmbVariavel->preencheCombo    ( $rsVariavel );
                $obCmbVariavel->obEvento->setOnChange("document.frm.$stValor.value='';");

                if ($stTipoVariavel=="BOOLEANO") {
                    $obFormulario->addComponente        ( $obCmbVariavel );
                    $stValidaParametros .= "if (f.".$stValor.") { if (f.".$stVariavel.".options[0].selected==true) { mensagem += \'@Campo $stNomVariavel deve ser informado!( )\';} }";
                } else {
                    $obFormulario->agrupaComponentes    ( array( $obTxtValor, $obCmbVariavel ) );
                    $stValidaParametros .= "if (f.".$stValor.") { if( (f.".$stValor.".value==0 && f.".$stValor.".value.length==0) && (f.".$stVariavel.".options[0].selected==true) ) { mensagem += \'@Campo $stNomVariavel deve ser informado!( )\';} }";
                }
                $prCount++;
                $rsParametros->proximo();
            }

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);

            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $stValidaParametros = "f = document.frm; mensagem=\'\';" . $stValidaParametros;
            $js  = "d.getElementById('spnFuncao').innerHTML = '".$stHtml."';";
            $js .= "f.stValidaParametros.value  = '$stValidaParametros';";
            $js .= "f.stFuncaoEval.value        = '".$stEval."';";
        } else {
            $js  = "d.getElementById('spnFuncao').innerHTML = '';";
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
        $obRegra->obRVariavel->setCodModulo( $rsFuncao->getCampo('cod_modulo') );
        $obRegra->obRVariavel->setCodBiblioteca( $rsFuncao->getCampo('cod_biblioteca') );
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

        $Condicao = Sessao::read('Condicao');
        $Condicao[] = ' '.$_GET['stFuncao'];
        Sessao::write('Condicao', $Condicao);

        $stHtml = implode(" ",$Condicao);
        $stHtml = str_replace(' ','&nbsp;',$stHtml);
        $stHtml = str_replace("'","\'",$stHtml);
        $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
        $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";
    break;
    case "Fechar":
        $js = "parent.window.close();";

    break;
//    case "TrataErros":
  //      $js = " echo teste";
    //    echo "AQUI";
//    break;
}
if($js)
    SistemaLegado::executaIFrameOculto($js);
?>
