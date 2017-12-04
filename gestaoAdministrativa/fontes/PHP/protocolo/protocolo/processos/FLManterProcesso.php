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
* Arquivo de instância para manutenção dos processos
* Data de Criação: 27/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 29043 $
$Name$
$Author: rodrigosoares $
$Date: 2008-04-07 17:19:28 -0300 (Seg, 07 Abr 2008) $

Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_PROT_CLASSES."componentes/ITextChaveProcesso.class.php" );
include_once( CAM_GA_PROT_CLASSES."componentes/ISelectClassificacaoAssunto.class.php");
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcesso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::remove('link');

$obForm = new Form();
$obForm->setAction($pgList);

$stSQL = "SELECT * FROM sw_atributo_protocolo";

$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$dbConfig->abreSelecao($stSQL);

if ($dbConfig->numeroDeLinhas > 0) {
    while (!($dbConfig->eof())) {
        $nomAtributo = $dbConfig->pegaCampo("nom_atributo");
        $tipo        = $dbConfig->pegaCampo("tipo");
        $valorLista  = $dbConfig->pegaCampo("valor_padrao");

        if ($tipo == "l") {
            $lista = explode("\n", $valorLista);
            $numValor = $dbConfig->pegaCampo("valor_padrao");
            $listaTipoCmb = explode("\n", $tipo);
        }
        if ($tipo == "t") {
            $stTexto = $dbConfig->pegaCampo("valor_padrao");
            $listaTipoTxt = explode("\n", $tipo);
        }
        if ($tipo == "n") {
            $numNumero = $dbConfig->pegaCampo("valor_padrao");
            $listaTipoNum = explode("\n", $tipo);
        }
        $dbConfig->vaiProximo();
    }
}

if ($lista == "") {
    $lista = array();
}
$rsLista = new RecordSet();
$rsLista->preenche($lista);

$obITextChaveProcesso = new ITextChaveProcesso();

$obISelectClassificacaoAssunto = new ISelectClassificacaoAssunto();
$obISelectClassificacaoAssunto->obTxtChave->setName         ( 'codClassifAssunto' );
$obISelectClassificacaoAssunto->obCmbClassificacao->setName ( 'codClassificacao'  );
$obISelectClassificacaoAssunto->obCmbAssunto->setName       ( 'codAssunto'        );

//RESUMO DO PROCESSO
$obTxtAssuntoReduzido = new TextBox;
$obTxtAssuntoReduzido->setName      ( 'stAssuntoReduzido' );
$obTxtAssuntoReduzido->setMaxLength ( 80                  );
$obTxtAssuntoReduzido->setSize      ( 80                  );
$obTxtAssuntoReduzido->setRotulo    ( 'Assunto Reduzido'  );
$obTxtAssuntoReduzido->setTitle     ( 'Descrição rápida do assunto do processo' );

//Componente que define o tipo de busca
$obTipoBuscaAssuntoReduzido = new TipoBusca( $obTxtAssuntoReduzido );

//BUSCA O INTERESSADO(POPUP CGM)
$obBuscaCGM = new IPopUpCGM( $obForm );
$obBuscaCGM->setRotulo               ( 'Interessado' );
$obBuscaCGM->obCampoCod->setName     ( 'numCgm'      );
$obBuscaCGM->setNull                 ( true          );

//COMPONENTES PARA O PERIDO DE BUSCA
$obDataInicial = new Data;
$obDataInicial->setName ( 'dataInicio'  );
$obDataFinal = new Data;
$obDataFinal->setName   ( 'dataTermino' );

$obFormulario = new Formulario();
$obFormulario->addTitulo("Dados para filtro");
$obFormulario->addForm($obForm);
$obFormulario->setAjuda('uc-01.06.98');
$obFormulario->addComponente($obITextChaveProcesso);
$obISelectClassificacaoAssunto->geraFormulario($obFormulario);
$obFormulario->addComponente( $obTipoBuscaAssuntoReduzido );
$obFormulario->addComponente( $obBuscaCGM );
$obFormulario->periodo($obDataInicial, $obDataFinal);
if ($dbConfig->numeroDeLinhas > 0) {
    $obFormulario->addTitulo('Atributos de Assunto de Processo');

    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($stSQL);
    if ($dbConfig->numeroDeLinhas > 0) {
        while (!($dbConfig->eof())) {
            $codAtributo = $dbConfig->pegaCampo("cod_atributo");
            $nomAtributo = $dbConfig->pegaCampo("nom_atributo");
            $tipo        = $dbConfig->pegaCampo("tipo");

            if ($tipo == "t") {
                $obTxtAtributosProcessos = new TextBox();
                $obTxtAtributosProcessos->setName("valorAtributoTxt[".$codAtributo."]");
                $obTxtAtributosProcessos->setSize('60');
                $obTxtAtributosProcessos->setMaxLength('50');
                $obTxtAtributosProcessos->setRotulo($nomAtributo);

                $obFormulario->addComponente($obTxtAtributosProcessos);
            }
            if ($tipo == "n") {
                $obTxtAtributosProcessosNum = new TextBox();
                $obTxtAtributosProcessosNum->setName("valorAtributoNum[".$codAtributo."]");
                $obTxtAtributosProcessosNum->setSize('60');
                $obTxtAtributosProcessosNum->setMaxLength('50');
                $obTxtAtributosProcessosNum->setRotulo($nomAtributo);

                $obFormulario->addComponente($obTxtAtributosProcessosNum);
            }
            if ($tipo == "l") {
                $obCmbAtributosProcesso = new Select();
                $obCmbAtributosProcesso->setName("valorAtributoCmb[".$codAtributo."]");
                $obCmbAtributosProcesso->setRotulo($nomAtributo);
                $obCmbAtributosProcesso->setStyle ( "width: 200px" );
                $obCmbAtributosProcesso->addOption ( '', 'Selecione' );
                while (list($key, $val) = each($lista)) {
                    $val = trim($val);
                    $obCmbAtributosProcesso->addOption($val, $val);
                }

                $obFormulario->addComponente($obCmbAtributosProcesso);
            }
            $dbConfig->vaiProximo();
        }
    }
}
$obFormulario->Ok();
$obFormulario->show();
?>
