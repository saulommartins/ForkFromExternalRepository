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
    * Formulário
    * Data de Criação: 03/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.12

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoCampos.class.php"                               );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoImportacao.class.php"                           );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoTamanhoFixo.class.php"                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDelimitadorColunas.class.php"                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoDelimitador.class.php"                          );

$stPrograma = "ManterConfiguracaoFormato";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

function montaSpanFormatoColunas()
{
    $obTPontoFormatoCampos = new TPontoFormatoCampos();
    $obTPontoFormatoCampos->recuperaTodos($rsFormatoCampos);

    $obFormulario = new Formulario();

    if ($_GET["boFormatoColuna"] == "FIXO") {
        $obFormulario->addTitulo( "EVAL_CAMPOS" );

        while ( !$rsFormatoCampos->eof() ) {
            $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');
            $stNomCampo = $rsFormatoCampos->getCampo('nom_campo');
            $stHint = "Informe a posição(Coluna) inicial e a posição(coluna) final do campo, no registro (linha)"
                    ." do arquivo de importação.";
            if ($inCodCampo == 1) {
                $stHint = "Informe a posição(Coluna) inicial e a posição(coluna) final do campo, no registro (linha)"
                    ." do arquivo de importação. Verificar o tamanho de matrícula/cartão ponto no cadastro do servidor.";
            }

            $obTxtPosicaoInicial = new TextBox;
            $obTxtPosicaoInicial->setRotulo              ( $stNomCampo                     );
            $obTxtPosicaoInicial->setTitle               ( $stHint                         );
            $obTxtPosicaoInicial->setName                ( "campo_".$inCodCampo."_inicial" );
            $obTxtPosicaoInicial->setId                  ( "campo_".$inCodCampo."_inicial" );
            $obTxtPosicaoInicial->setSize                ( 5                               );
            $obTxtPosicaoInicial->setMaxLength           ( 4                               );
            $obTxtPosicaoInicial->setInteiro             ( true                            );
            $obTxtPosicaoInicial->setNullBarra           ( false                           );

            $obTxtPosicaoFinal = new TextBox;
            $obTxtPosicaoFinal->setRotulo              ( $stNomCampo                     );
            $obTxtPosicaoFinal->setTitle               ( $stHint                         );
            $obTxtPosicaoFinal->setName                ( "campo_".$inCodCampo."_final"   );
            $obTxtPosicaoFinal->setId                  ( "campo_".$inCodCampo."_final"   );
            $obTxtPosicaoFinal->setSize                ( 5                               );
            $obTxtPosicaoFinal->setMaxLength           ( 4                               );
            $obTxtPosicaoFinal->setInteiro             ( true                            );
            $obTxtPosicaoFinal->setNullBarra           ( false                           );
            $obTxtPosicaoFinal->obEvento->setOnChange  ( "montaParametrosGET('sugirirPosicaoInicial');" );

            $obFormulario->agrupaComponentes ( array($obTxtPosicaoInicial, $obTxtPosicaoFinal));

            $rsFormatoCampos->proximo();
        }
    } elseif ($_GET["boFormatoColuna"] == "DELIMITADOR") {
        $obTPontoFormatoCampos->recuperaTodos($rsFormatoCamposCombo);

        $obTxtDelimitador = new TextBox;
        $obTxtDelimitador->setRotulo       ( "Delimitador de Coluna"         );
        $obTxtDelimitador->setTitle        ( "Informe o caracter delimitar das colunas no arquivo. Não utilizar o caracter '#'." );
        $obTxtDelimitador->setName         ( "stDelimitador"                 );
        $obTxtDelimitador->setId           ( "stDelimitador"                 );
        $obTxtDelimitador->setSize         ( 5                               );
        $obTxtDelimitador->setMaxLength    ( 1                               );
        $obTxtDelimitador->setNullBarra    ( false                           );
        $obTxtDelimitador->setCaracteresAceitos("[^#]");

        $obFormulario->addComponente($obTxtDelimitador);

        while ( !$rsFormatoCampos->eof() ) {
            $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');
            $stNomCampo = $rsFormatoCampos->getCampo('nom_campo');

            $obCmbOrdemCampos = new Select();
            $obCmbOrdemCampos->setTitle      ( "Selecione na posição do arquivo, o respectivo campo para importação."  );
            $obCmbOrdemCampos->setRotulo     ( "Coluna ".$rsFormatoCampos->getCorrente()                               );
            $obCmbOrdemCampos->setName       ( "coluna_".$inCodCampo                                                   );
            $obCmbOrdemCampos->setId         ( "coluna_".$inCodCampo                                                   );
            $obCmbOrdemCampos->setNullBarra  ( false                                                                   );
            $obCmbOrdemCampos->setCampoDesc  ( "nom_campo"                                                             );
            $obCmbOrdemCampos->setCampoId    ( "cod_campo"                                                             );
            if ($inCodCampo > 6) {
                $obCmbOrdemCampos->addOption ( "", "Selecione"                                                         );
            } else {
                $obCmbOrdemCampos->setValue      ( $inCodCampo                                                         );
            }
            $obCmbOrdemCampos->preencheCombo ( $rsFormatoCamposCombo                                                   );

            $obFormulario->addComponente($obCmbOrdemCampos);

            $rsFormatoCampos->proximo();
        }
    }

    $obFormulario->montaInnerHTML();
    $stHTML = $obFormulario->getHTML();
    $teste = '<td colspan="2" class="alt_dados">EVAL_CAMPOS</td>';
    $teste2 = '<td colspan="2" class="alt_dados">Campos</td><td colspan="2" class="alt_dados">Inicial &nbsp; &nbsp; Final</td>';

    $stHTML = str_replace($teste, $teste2, $stHTML);
    $stJs .= "jQuery('#spnInfFormatoColunas').html('".$stHTML."');     \n";

    return $stJs;
}

function incluirConfiguracaoImportacao()
{
   $obTPontoFormatoCampos = new TPontoFormatoCampos();
   $obTPontoFormatoCampos->recuperaTodos($rsFormatoCampos);

   $obErro = validaFormatosImportacao($rsFormatoCampos);

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arFormatosCadastrados = Sessao::read('arFormatosCadastrados');

        $arElementos["inId"]                 = count($arFormatosCadastrados) + 1;
        $arElementos["stDescricao"]          = $_GET["stDescricao"];
        $arElementos["boReferenciaCadastro"] = $_GET["boReferenciaCadastro"];
        $arElementos["boFormatoColuna"]      = $_GET["boFormatoColuna"];
        $arElementos["cod_formato"]          = "";

        $rsFormatoCampos->setPrimeiroElemento();
        if ($_GET["boFormatoColuna"] == "FIXO") {
            while (!$rsFormatoCampos->eof()) {
                $inCodCampo = $rsFormatoCampos->getCampo("cod_campo");

                $arElementos["fixo"]["posicao_inicial"][$inCodCampo] = $_GET["campo_".$inCodCampo."_inicial"];
                $arElementos["fixo"]["posicao_final"][$inCodCampo]   = $_GET["campo_".$inCodCampo."_final"];

                $rsFormatoCampos->proximo();
            }
        } else {
            $arElementos["delimitador"]["delimitador"] = $_GET["stDelimitador"];
            while (!$rsFormatoCampos->eof()) {
                $inCodCampo = $rsFormatoCampos->getCampo("cod_campo");
                $arElementos["delimitador"]["coluna"][$inCodCampo] = $_GET["coluna_".$inCodCampo];

                $rsFormatoCampos->proximo();
            }
        }

        $arFormatosCadastrados[] = $arElementos;

        Sessao::write('arFormatosCadastrados', $arFormatosCadastrados);
        $stJs .= montaListaFormatos( $arFormatosCadastrados );
        $stJs .= limpaCampos( $rsFormatoCampos, true );
    }

    return $stJs;
}

function alterarConfiguracaoImportacao()
{
    $inId = $_REQUEST["inId"];

    $obTPontoFormatoCampos = new TPontoFormatoCampos();
    $obTPontoFormatoCampos->recuperaTodos($rsFormatoCampos);

    $obErro = validaFormatosImportacao($rsFormatoCampos);

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arFormatosCadastrados = Sessao::read('arFormatosCadastrados');

        foreach ($arFormatosCadastrados as $campo => $valor) {
            if ($valor["inId"] == $inId) {
                $arFormatosCadastrados[$campo]["stDescricao"]          = $_GET["stDescricao"];
                $arFormatosCadastrados[$campo]["boReferenciaCadastro"] = $_GET["boReferenciaCadastro"];
                $arFormatosCadastrados[$campo]["boFormatoColuna"]      = $_GET["boFormatoColuna"];

                $rsFormatoCampos->setPrimeiroElemento();
                while (!$rsFormatoCampos->eof()) {
                    $inCodCampo = $rsFormatoCampos->getCampo("cod_campo");
                    $stNomCampo = $rsFormatoCampos->getCampo("nom_campo");

                    if ($_GET["boFormatoColuna"] == "FIXO") {
                        $arFormatosCadastrados[$campo]["fixo"]["posicao_inicial"][$inCodCampo] = $_GET["campo_".$inCodCampo."_inicial"];
                        $arFormatosCadastrados[$campo]["fixo"]["posicao_final"][$inCodCampo]   = $_GET["campo_".$inCodCampo."_final"];
                    } else {
                        $arFormatosCadastrados[$campo]["delimitador"]["delimitador"] = $_GET["stDelimitador"];
                        $arFormatosCadastrados[$campo]["delimitador"]["coluna"][$inCodCampo] = $_GET["coluna_".$inCodCampo];
                    }
                    $rsFormatoCampos->proximo();
                }
            }
        }
        Sessao::write('arFormatosCadastrados', $arFormatosCadastrados);
        $stJs .= montaListaFormatos( $arFormatosCadastrados );
        $stJs .= limpaCampos( $rsFormatoCampos, true );
        #$stJs .= " limpaFormularioConfiguracaoImportacao(); ";
    }

    return $stJs;
}

function excluirConfiguracaoImportacao()
{
    $arTMP = array ();
    $id = $_GET["inId"];
    $arFormatosCadastrados = Sessao::read("arFormatosCadastrados");
    Sessao::remove("arFormatosCadastrados");

    foreach ($arFormatosCadastrados as $campo => $valor) {
        if ($valor["inId"] != $id) {
            $arTMP[] = $valor;
        }
    }
    Sessao::write("arFormatosCadastrados", $arTMP);
    $stJs = montaListaFormatos( $arTMP );
    $stJs .= limpaCampos( $rsFormatoCampos, true );

    return $stJs;
}

function carregaConfiguracaoImportacao()
{
    $arFormatosCadastrados = Sessao::read("arFormatosCadastrados");
    $inId  = $_REQUEST["inId"];

    $stJs .= " jQuery('#boFormatoColunaFixo').attr('disabled', '');         \n";
    $stJs .= " jQuery('#boFormatoColunaDelimitador').attr('disabled', '');  \n";

    foreach ($arFormatosCadastrados as $chave => $dadosConfiguracao) {
        if ((int) $inId == (int) $dadosConfiguracao["inId"]) {
            $stJs .= " jQuery('#stDescricao').val('".$dadosConfiguracao['stDescricao']."');  \n";
            $stJs .= " jQuery('#inId').val('".$inId."');                                     \n";

            if ($dadosConfiguracao["boReferenciaCadastro"] == "MATRICULA") {
                $stJs .= " jQuery('#boReferenciaCadastroMatricula').attr('checked', 'checked');   \n";
            } else {
                $stJs .= " jQuery('#boReferenciaCadastroCartaoPonto').attr('checked', 'checked'); \n";
            }

            if ($dadosConfiguracao["boFormatoColuna"] == "FIXO") {
                $_GET["boFormatoColuna"] = "FIXO";
                $stJs .= " jQuery('#boFormatoColunaFixo').attr('checked', 'checked');           \n";
                $stJs .= " jQuery('#boFormatoColunaDelimitador').attr('disabled', 'disabled');  \n";
            } else {
                $_GET["boFormatoColuna"] = "DELIMITADOR";
                $stJs .= " jQuery('#boFormatoColunaDelimitador').attr('checked', 'checked');     \n";
                $stJs .= " jQuery('#boFormatoColunaFixo').attr('disabled', 'disabled');          \n";

            }
            $stJs .= montaSpanFormatoColunas();

            if ( trim($dadosConfiguracao["boFormatoColuna"]) == "FIXO" ) {
                foreach ($dadosConfiguracao["fixo"]["posicao_inicial"] as $inCodCampo => $value) {
                    $stJs .= "jQuery('#campo_".$inCodCampo."_inicial').val('".$dadosConfiguracao["fixo"]["posicao_inicial"][$inCodCampo]."');     \n";
                    $stJs .= "jQuery('#campo_".$inCodCampo."_final').val('".$dadosConfiguracao["fixo"]["posicao_final"][$inCodCampo]."');         \n";
                }
            } else {
                $stJs .= " jQuery('#stDelimitador').val('".$dadosConfiguracao["delimitador"]["delimitador"]."');  ";
                foreach ($dadosConfiguracao["delimitador"]["coluna"] as $inCodCampo => $value) {
                    $stJs .= " jQuery('#coluna_".$inCodCampo."').val('".$value."');";
                }
            }
        }
    }
    $stJs .= " jQuery('#btIncluirConfiguracaoImportacao').attr('disabled', 'disabled');        \n";
    $stJs .= " jQuery('#btAlterarConfiguracaoImportacao').removeAttr('disabled');                \n";
    $stJs .= " jQuery('#btAlterarConfiguracaoImportacao').attr('onClick', 'montaParametrosGET(\'alterarConfiguracaoImportacao\');' );";

    return $stJs;
}

function validaFormatosImportacao($rsFormatoCampos)
{
    $obErro = new erro();
    $arFormatosCadastrados = Sessao::read('arFormatosCadastrados');

    # Verifica se o nome informa já não existe na lista
    foreach ($arFormatosCadastrados as $chave => $arFormatos) {
        if (trim($_GET["stDescricao"]) == trim($arFormatos["stDescricao"])) {
            if (trim($_GET["stCtrl"]) == "alterarConfiguracaoImportacao") {
                if (trim($_GET["inId"])!=trim($arFormatos["inId"])) {
                    $obErro->setDescricao($obErro->getDescricao()."@Informe outra descrição, pois a informada já existe na lista de formatos.");
                }
            } else {
                $obErro->setDescricao($obErro->getDescricao()."@Informe outra descrição, pois a informada já existe na lista de formatos.");
            }
        }
    }

    $rsFormatoCampos->setPrimeiroElemento();
    if ($_GET["boFormatoColuna"] == "FIXO") {
        while ( !$rsFormatoCampos->eof() ) {

            $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');
            $stNomCampo = $rsFormatoCampos->getCampo('nom_campo');

            $inicio = (int) $_GET["campo_".$inCodCampo."_inicial"];
            $fim    = (int) $_GET["campo_".$inCodCampo."_final"];

            if ( trim($_GET["campo_".$inCodCampo."_inicial"])=="" && $inCodCampo < 7 ) {
                $obErro->setDescricao($obErro->getDescricao()."@Deve ser informado a posição inicial para o campo ".$stNomCampo);
            }

            if ( trim($_GET["campo_".$inCodCampo."_final"])=="" && $inCodCampo < 7 ) {
                $obErro->setDescricao($obErro->getDescricao()."@Deve ser informado a posição final para o campo ".$stNomCampo);
            }

            if ( trim($_GET["campo_".$inCodCampo."_final"])!="" && trim($_GET["campo_".$inCodCampo."_inicial"])!="" ) {
                if ($inicio >= $fim) {
                    $obErro->setDescricao($obErro->getDescricao()."@Posição final deve ser maior do que a posição inicial para o campo ".$stNomCampo);
                }
            }

            //Verifica posição final com a inicial de baixo.
            $rsFormatoCampos->proximo();
            $inCodCampoProx = $rsFormatoCampos->getCampo('cod_campo');
            $stNomCampoProx = $rsFormatoCampos->getCampo('nom_campo');

            if (trim($_GET["campo_".$inCodCampo."_final"])!="" && trim($_GET["campo_".$inCodCampo."_inicial"])!="") {
                $inicioProx = (int) $_GET["campo_".$inCodCampoProx."_inicial"];
            } else {
                //verifica se preencheu apenas inicial ou final quando campo não é obrigatório
                if ($inCodCampo > 6) {
                    if (trim($_GET["campo_".$inCodCampo."_final"])!="" || trim($_GET["campo_".$inCodCampo."_inicial"])!="") {
                        $obErro->setDescricao($obErro->getDescricao()."@Posição Inicial e final para o campo ".$stNomCampo." devem ser informados.");
                    }
                }
            }
            $rsFormatoCampos->anterior();

            // Verifica para os campos não obrigatorio se foi selecionado HH e MM
            if ($inCodCampo > 6) {
                if ( trim($_GET["campo_".$inCodCampo."_final"])!="" && trim($_GET["campo_".$inCodCampo."_inicial"])!="" ) {
                    if ($inCodCampo % 2 != 0) {
                        $rsFormatoCampos->proximo();

                        $inCodCampoProx = $rsFormatoCampos->getCampo('cod_campo');
                        $stNomCampoProx = $rsFormatoCampos->getCampo('nom_campo');

                        if ( trim($_GET["campo_".$inCodCampoProx."_final"])=="" || trim($_GET["campo_".$inCodCampoProx."_inicial"])=="" ) {
                            $obErro->setDescricao($obErro->getDescricao()."@A posição inicial e final do campo ".$stNomCampoProx." deve ser informada para completar o horário.");
                        }
                        $rsFormatoCampos->anterior();
                    }
                }
            }

            $rsFormatoCampos->proximo();
        }
    } elseif ($_GET["boFormatoColuna"] == "DELIMITADOR") {
        $arTemp = array();

        if ( trim($_GET["stDelimitador"])=="" ) {
            $obErro->setDescricao("Campo Delimitador de Coluna deve ser informado.");
        }

        if ( !$obErro->ocorreu() ) {
            while ( !$rsFormatoCampos->eof() ) {
                $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');

                // verifica campos selecionados mais de uma vez
                foreach ($_GET as $chave => $valor) {
                    if ( trim($chave) == trim("coluna_".$inCodCampo) && trim($valor)!="") {

                        $arTemp[$valor] = $arTemp[$valor] + 1;

                        if ($arTemp[$valor]>1) {
                            $obErro->setDescricao($obErro->getDescricao()."@Coluna ".$inCodCampo."(".recuperaDescricaoCampo($valor).") não pode ser selecionada duas vezes para importação");
                        }
                    }
                }

                // Verifica para os campos não obrigatorio se foi selecionado HH e MM
                if (!$obErro->ocorreu()) {
                    if ($inCodCampo > 6) {
                        if ( trim($_GET["coluna_".$inCodCampo])!="" ) {
                            if ($inCodCampo % 2 != 0) {

                                $rsFormatoCampos->proximo();
                                $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');

                                if ( trim($_GET["coluna_".$inCodCampo])=="" ) {
                                    $obErro->setDescricao($obErro->getDescricao()."@Coluna ".$inCodCampo."(".recuperaDescricaoCampo($inCodCampo).") deve ser informada para completar o horário.");
                                }
                                $rsFormatoCampos->anterior();
                            }
                        }
                    }
                }
                $rsFormatoCampos->proximo();
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = verificaCampoObrigatoriosDelimitador($arTemp);
        }
    }

    return $obErro;
}

function verificaCampoObrigatoriosDelimitador($arSelecionados)
{
    $obErro = new erro();

    for ($i=1; $i<7; $i++) {
        if (!isset($arSelecionados[$i])) {
            $obErro->setDescricao("O campo ".recuperaDescricaoCampo($i)." deve ser selecionado, pois é um campo obrigatório");
        }
    }

    return $obErro;
}

function recuperaDescricaoCampo($inCodCampo)
{
    $stFiltro = " WHERE cod_campo = ".$inCodCampo;
    $obTPontoFormatoCampos = new TPontoFormatoCampos();
    $obTPontoFormatoCampos->recuperaTodos($rsFormatoCampos, $stFiltro);

    return $rsFormatoCampos->getCampo("nom_campo");
}

function limpaCampos($rsFormatoCampos="", $novaConfig=false)
{
    if (!is_object($rsFormatoCampos)) {
        $obTPontoFormatoCampos = new TPontoFormatoCampos();
        $obTPontoFormatoCampos->recuperaTodos($rsFormatoCampos);
    }

    $rsFormatoCampos->setPrimeiroElemento();

    while ( !$rsFormatoCampos->eof() ) {
        $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');

        if ($_GET["boFormatoColuna"] == "FIXO") {
            $stJs .= "jQuery('#campo_".$inCodCampo."_inicial').val('');         \n";
            $stJs .= "jQuery('#campo_".$inCodCampo."_final').val('');           \n";
        } else {
            $stJs .= "jQuery('#stDelimitador').val('');                         \n";
            if ($inCodCampo < 7) {
                $stJs .= "jQuery('#coluna_".$inCodCampo."').val('".$inCodCampo."'); \n";
            } else {
                $stJs .= "jQuery('#coluna_".$inCodCampo."').val(''); \n";
            }
        }
        $rsFormatoCampos->proximo();
    }
    if ($novaConfig===true) {
        $_GET["boFormatoColuna"] = "FIXO";
        $stJs .= montaSpanFormatoColunas();
        $stJs .= " jQuery('#stDescricao').val('');                                             \n";
        $stJs .= " jQuery('#btIncluirConfiguracaoImportacao').attr('disabled', '');            \n";
        $stJs .= " jQuery('#btAlterarConfiguracaoImportacao').attr('disabled', 'disabled');    \n";
        $stJs .= " jQuery('#boFormatoColunaDelimitador').attr('disabled', '');                 \n";
        $stJs .= " jQuery('#boFormatoColunaFixo').attr('checked', 'checked');                  \n";
        $stJs .= " jQuery('#boFormatoColunaFixo').attr('disabled', '');                        \n";
    }

    return $stJs;
}

function sugirirPosicaoInicial()
{
    $obTPontoFormatoCampos = new TPontoFormatoCampos();
    $obTPontoFormatoCampos->recuperaTodos($rsFormatoCampos);

    while (!$rsFormatoCampos->eof()) {
        $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');

        if ( trim($_GET["campo_".$inCodCampo."_final"])!="" ) {

            $inValorSugestao = $_GET["campo_".$inCodCampo."_final"] + 1;

            $rsFormatoCampos->proximo();
            $inCodCampo = $rsFormatoCampos->getCampo('cod_campo');

            if ( trim($_GET["campo_".$inCodCampo."_inicial"])=="" ) {
                if ( trim($inCodCampo)!="" ) {
                    $stJs .= "jQuery('#campo_".$inCodCampo."_inicial').val('".$inValorSugestao."'); \n";
                    $stJs .= "jQuery('#campo_".$inCodCampo."_final').focus();                       \n";
                    break;
                }
            }
            $rsFormatoCampos->anterior();
        }
        $rsFormatoCampos->proximo();
    }

    return $stJs;

}

function montaListaFormatos($arRecordSet)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );
    $stLink .= "&stAcao=".$_REQUEST["stAcao"]."&stDescricao=".$_GET["stDescricao"];

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Formatos Cadastrados" );
        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome" );
        $obLista->ultimoCabecalho->setWidth( 80 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ação" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stDescricao");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setLinkId("alterar");
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('carregaConfiguracaoImportacao');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->addCampo("2","cod_formato");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirConfiguracaoImportacao');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs = "jQuery('#spnListaFormatos').html('".$stHtml."');";

    return $stJs;
}

function carregaListaFormatos()
{
    $arFormatosCadastrados = array();

    $stOrdem = "descricao";
    $obTPontoFormatoImportacao = new TPontoFormatoImportacao();
    $obTPontoFormatoImportacao->recuperaTodos($rsFormatoImportacao, "", $stOrdem);

    while (!$rsFormatoImportacao->eof()) {
        $arTemp = array();

        $arTemp["inId"]                 = count($arFormatosCadastrados) + 1;
        $arTemp["stDescricao"]          = $rsFormatoImportacao->getCampo("descricao");
        $arTemp["cod_formato"]          = $rsFormatoImportacao->getCampo("cod_formato");
        $arTemp["boReferenciaCadastro"] = ($rsFormatoImportacao->getCampo("referencia_cadastro")=="M"?"MATRICULA":"CARTAO_PONTO");
        $arTemp["boFormatoColuna"]      = ($rsFormatoImportacao->getCampo("formato_colunas")=="T"?"FIXO":"DELIMITADOR");

        $obTPontoFormatoTamanhoFixo = new TPontoFormatoTamanhoFixo();
        $stFiltro = " WHERE cod_formato = ". $rsFormatoImportacao->getCampo("cod_formato");
        $obTPontoFormatoTamanhoFixo->recuperaTodos($rsFormatoTamanhoFixo, $stFiltro);

        if (trim($arTemp["boFormatoColuna"]) == "FIXO") {
            while (!$rsFormatoTamanhoFixo->eof()) {
                $inCodCampo = $rsFormatoTamanhoFixo->getCampo("cod_campo");

                $arTemp["fixo"]["posicao_inicial"][$inCodCampo] = $rsFormatoTamanhoFixo->getCampo("posicao_inicial");
                $arTemp["fixo"]["posicao_final"][$inCodCampo]   = $rsFormatoTamanhoFixo->getCampo("posicao_final");

                $rsFormatoTamanhoFixo->proximo();
            }

        }

        if (trim($arTemp["boFormatoColuna"]) == "DELIMITADOR") {
            $obTPontoFormatoDelimitador = new TPontoFormatoDelimitador();
            $stFiltro = " WHERE cod_formato = ". $rsFormatoImportacao->getCampo("cod_formato");
            $obTPontoFormatoDelimitador->recuperaTodos($rsFormatoDelimitador, $stFiltro);

            $arTemp["delimitador"]["delimitador"] = $rsFormatoDelimitador->getCampo("formato_delimitador");

            $obTPontoDelimitadorColunas = new TPontoDelimitadorColunas();
            $stFiltro = " WHERE cod_formato = ". $rsFormatoImportacao->getCampo("cod_formato");
            $obTPontoDelimitadorColunas->recuperaTodos($rsDelimitadorColunas, $stFiltro);

            while (!$rsDelimitadorColunas->eof()) {
                $inCodCampo = $rsDelimitadorColunas->getCampo("cod_campo");

                $arTemp["delimitador"]["coluna"][$inCodCampo] = $rsDelimitadorColunas->getCampo("coluna");

                $rsDelimitadorColunas->proximo();
            }
        }

        $arFormatosCadastrados[] = $arTemp;

        $rsFormatoImportacao->proximo();
    }

    Sessao::write('arFormatosCadastrados', $arFormatosCadastrados);
    $stJs .= montaListaFormatos($arFormatosCadastrados);

    return $stJs;
}

function processaOnLoad()
{
    $stJs  = montaSpanFormatoColunas();
    $stJs .= carregaListaFormatos();
    $stJs .= " jQuery('#btLimparConfiguracaoImportacao').attr('onClick', 'montaParametrosGET(\'limpaCampos\');' );                     \n";
    $stJs .= " jQuery('#btAlterarConfiguracaoImportacao').attr('onClick', 'montaParametrosGET(\'alterarConfiguracaoImportacao\');' );  \n";
    $stJs .= " jQuery('#btIncluirConfiguracaoImportacao').attr('onClick', 'montaParametrosGET(\'incluirConfiguracaoImportacao\');' );  \n";
    $stJs .= " jQuery('#limpar').attr('onClick', ' jQuery(\'#btIncluirConfiguracaoImportacao\').attr(\'disabled\', \'\');'         );  \n";
    $stJs .= " jQuery('#limpar').click( function () {
                                        jQuery('#btAlterarConfiguracaoImportacao').attr('disabled', 'disabled');
                                        montaParametrosGET('carregaListaFormatos');
                                 } );     \n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "incluirConfiguracaoImportacao":
        $stJs .= incluirConfiguracaoImportacao();
        break;
    case "alterarConfiguracaoImportacao":
        $stJs .= alterarConfiguracaoImportacao();
        break;
    case "excluirConfiguracaoImportacao":
        $stJs .= excluirConfiguracaoImportacao();
        break;
    case "carregaConfiguracaoImportacao":
        $stJs .= carregaConfiguracaoImportacao();
        break;
    case "montaSpanFormatoColunas":
        $stJs .= montaSpanFormatoColunas();
        break;
    case "sugirirPosicaoInicial":
        $stJs .= sugirirPosicaoInicial();
        break;
    case "limpaCampos":
        $stJs .= limpaCampos("", true);
        break;
    case "carregaListaFormatos":
        $stJs .= carregaListaFormatos();
        break;
    case "processaOnLoad":
        $stJs .= processaOnLoad();
        break;
}

if($stJs)
   echo($stJs);
?>
