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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

$Id: filtrosProcessoLegado.inc.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.01.00
*/

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

include_once( CAM_GA_PROT_CLASSES."componentes/ISelectClassificacaoAssunto.class.php" );
include_once( CAM_GA_PROT_CLASSES."componentes/ITextChaveProcesso.class.php" );
include_once( CAM_GA_PROT_CLASSES."componentes/IPopUpProcesso.class.php" );
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
//FORM
$obForm = new Form;
$obForm->setAction( $_SERVER['PHP_SELF'] );
//
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "controle" );
$obHdnCtrl->setValue( 1 );

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
if (empty($lista)) {
    $lista = array();
}
$rsLista = new RecordSet();
$rsLista->preenche($lista);

//OBJETO CRIADO PARA ATENDER AS DIVERSAS SITUAÇÕES ONDE ESTE FORMULÁRIO É USADO
if ( isset( $stAuxNome ) ) {
    $obHdnAuxiliar = new Hidden;
    $obHdnAuxiliar->setName ( $stAuxNome  );
    $obHdnAuxiliar->setValue( $stAuxValor );
} else {
    $obHdnAuxiliar = null;
}

$obBuscaProcesso = new IPopUpProcesso( $obForm );

//CHAVE DO PROCESSO
$obChaveProcesso = new ITextChaveProcesso;
$obChaveProcesso->setName( "codProcessoFl" );

//COMPONENTE COM OS COMBOS DE CLASSIFICAÇÃO E ASSUNTO
$obClassAssunto = new ISelectClassificacaoAssunto;
$obClassAssunto->obTxtChave->setName         ( 'codClassifAssunto' );
$obClassAssunto->obCmbClassificacao->setName ( 'codClassificacao'  );
$obClassAssunto->obCmbAssunto->setName       ( 'codAssunto'        );

//RESUMO DO PROCESSO
$obTxtAssuntoReduzido = new TextBox;
$obTxtAssuntoReduzido->setName      ( 'resumo'           );
$obTxtAssuntoReduzido->setMaxLength ( 80                 );
$obTxtAssuntoReduzido->setSize      ( 80                 );
$obTxtAssuntoReduzido->setRotulo    ( 'Assunto Reduzido' );
$obTxtAssuntoReduzido->setTitle     ( 'Descrição rápida do assunto do processo' );

//BUSCA O INTERESSADO(POPUP CGM)
$obBuscaCGM = new IPopUpCGM( $obForm );
$obBuscaCGM->setRotulo               ( 'Interessado' );
$obBuscaCGM->obCampoCod->setName     ( 'numCgm'      );
$obBuscaCGM->obCampoCod->setTabIndex ( ''            );
$obBuscaCGM->setNull                 ( true          );

//COMPONENTES PARA O PERIDO DE BUSCA
$obDataInicial = new Data;
$obDataInicial->setName ( 'dataInicio'  );
$obDataFinal = new Data;
$obDataFinal->setName   ( 'dataTermino' );

//COMBO QUE DEFINE A ORDER QUE SERA FEITA BUSCA
$obSelectOrdem = new Select;
$obSelectOrdem->setName   ( 'ordem' );
$obSelectOrdem->setRotulo ( 'Ordem' );
$obSelectOrdem->addOption ( 1, 'Código/Exercício'      );
$obSelectOrdem->addOption ( 2, 'Nome'                  );
$obSelectOrdem->addOption ( 3, 'Classificação/Assunto' );
$obSelectOrdem->addOption ( 4, 'Data'                  );

$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm               );
$obFormulario->addHidden         ( $obHdnCtrl            );
if ( is_object( $obHdnAuxiliar ) ) {
    $obFormulario->addHidden     ( $obHdnAuxiliar        );
}
$obFormulario->addTitulo         ( 'Dados para filtro'   );

    $obFormulario->addComponente     ( $obBuscaProcesso      );

//$obFormulario->addComponente     ( $obChaveProcesso );

$obClassAssunto->geraFormulario  ( $obFormulario         );
$obFormulario->addComponente     ( $obTxtAssuntoReduzido );
$obFormulario->addComponente     ( $obBuscaCGM           );
$obFormulario->periodo           ( $obDataInicial, $obDataFinal );
$obFormulario->addComponente     ( $obSelectOrdem        );
if ($dbConfig->numeroDeLinhas > 0) {
    $obFormulario->addTitulo         ( 'Atributos de Assunto de Processo'   );
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
$obFormulario->Ok                ();
$obFormulario->show              ();
?>
