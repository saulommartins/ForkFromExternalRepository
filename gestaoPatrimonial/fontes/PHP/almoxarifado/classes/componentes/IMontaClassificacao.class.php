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
    * Classe de regra de interface para Classificação
    * Data de Criação: 05/12/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Interface

    $Id: IMontaClassificacao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

class IMontaClassificacao extends Objeto
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoCatalogo;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivel;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoClassificacao;
/**
    * @access Private
    * @var String
*/
var $stMascara;
/**
    * @access Private
    * @var String
*/
var $stCodEstrutural;
/**
    * @access Private
    * @var Object
*/
var $obRAlmoxarifadoClassificacao;
/**
    * @access Private
    * @var Boolean
*/
var $boUltimoNivelRequerido;
/**
    * @access Private
    * @var Boolean
*/
var $boClassificacaoRequerida;
/**
    * @access Private
    * @var Boolean
*/
var $boReadOnly;
/**
    * @access Private
    * @var String
*/
var $stOnChangeCombo;
/**
    * @access Private
    * @var boolean
*/
var $boComboCompleta;

/**
    * @access Private
    * @var String
*/
var $stCodEstruturalReduzido;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoCatalogo($valor) { $this->inCodigoCatalogo = $valor; }

/**
    * @access Public
    * @param boolean $boComboCompleta
*/
function setComboClassificacaoCompleta($boComboCompleta) { $this->boComboCompleta = $boComboCompleta;}

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel       = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoClassificacao($valor) { $this->inCodigoClassificacao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodEstrutural($valor) { $this->stCodEstrutural= $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodEstruturalReduzido($valor) { $this->stCodEstruturalReduzido= $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setUltimoNivelRequerido($valor) { $this->boUltimoNivelRequerido = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setClassificacaoRequerida($valor) { $this->boClassificacaoRequerida = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setReadOnly($valor) { $this->boReadOnly = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnChangeCombo($valor) { $this->stOnChangeCombo = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoCatalogo() { return $this->inCodigoCatalogo;  }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;     }
/**
    * @access Public
    * @return Integer
*/
function getCodigoClassificacao() { return $this->inCodigoClassificacao; }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;         }
/**
    * @access Public
    * @return String
*/
function getCodEstrutural() { return $this->stCodEstrutural;   }
/**
    * @access Public
    * @return Boolean
*/
function getUltimoNivelRequerido() { return $this->boUltimoNivelRequerido;   }
/**
    * @access Public
    * @return Boolean
*/
function getClassificacaoRequerida() { return $this->boClassificacaoRequerida;   }
/**
    * @access Public
    * @return Boolean
*/
function getReadOnly() { return $this->boReadOnly;   }

/**
    * @access Public
    * @return boolean $boComboCompleta
*/
function getComboClassificacaoCompleta() { return $this->boComboCompleta;}

/**
     * Método construtor
     * @access Private
*/
function IMontaClassificacao()
{
   include_once( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php");
    $this->obRAlmoxarifadoClassificacao = new RAlmoxarifadoCatalogoClassificacao();
    $this->stMascara                    = "";
    $this->boUltimoNivelRequerido       = false;
    $this->boClassificacaoRequerida     = true;
    $this->boReadOnly                   = false;
}

/**
    * Monta os combos de classificação conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $pgOcul  = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaClassificacao.php?'.Sessao::getId();

    $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo( $this->inCodigoCatalogo );
    $obErro = $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->listarNiveis( $rsListaNivel );
    $arCombosClassificacao = array();
    $boFlagPrimeiroNivel = true;
    $inContNomeCombo = 1;
    while ( !$rsListaNivel->eof() ) {
        $inNumNivel = $rsListaNivel->getCampo( "nivel" );
        $stNomeNivel[$inNumNivel] = $rsListaNivel->getCampo( "descricao" );
        //DEFINICAO PADRAO DOS COMBOS DE CLASSIFICAÇÃO
        $obCmbClassificacao = new Select;
        $obCmbClassificacao->setRotulo    ( "$stNomeNivel[$inNumNivel]"     );
        $obCmbClassificacao->setTitle     ( "Selecione a classificação." );
        $obCmbClassificacao->setNull      ( !$this->boUltimoNivelRequerido );
        $obCmbClassificacao->addOption    ( "", "Selecione"   );
        $obCmbClassificacao->setCampoId   ( "[nivel]-[cod_classificacao]-[cod_estrutural]-[cod_nivel]" );
        $obCmbClassificacao->setCampoDesc ( "descricao" );
        $obCmbClassificacao->setStyle     ( "width:250px"     );
        $obCmbClassificacao->setName( "inCodClassificacao_".$inContNomeCombo );
        $onChange = "ajaxJavaScript('".$pgOcul."&inPosicaoClassificacao=".($inContNomeCombo+1)."&inCodCatalogo='+document.frm.inCodCatalogo.value+'&inNumNiveisClassificacao='+document.frm.inNumNiveisClassificacao.value+'&inCodClassificacao_".$inContNomeCombo."='+this.value, 'preencheProxComboClassificacao');" ;
        $inContNomeCombo++;
        //PREENCHE APENAS O PRIMEIRO NIVEL
        if ($boFlagPrimeiroNivel) {
           $boFlagPrimeiroNivel = false;
           $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->addCatalogoNivel();
           $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel ( $rsListaNivel->getCampo("nivel") );
           $obErro = $this->obRAlmoxarifadoClassificacao->listarDetalhesClassificacao( $rsListaClassificacao );

           while ( !$rsListaClassificacao->eof() ) {
               Sessao::write("nomFiltro['nom_classificacao'][".$rsListaClassificacao->getCampo( 'nivel' )."]", $rsListaClassificacao->getCampo( 'descricao' ));
               $rsListaClassificacao->proximo();
           }

            $rsListaClassificacao->setPrimeiroElemento();
            $obCmbClassificacao->preencheCombo( $rsListaClassificacao );
        }
        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $obCmbClassificacao->obEvento->setOnChange ( $onChange.$this->stOnChangeCombo );
        $arCombosClassificacao[] = $obCmbClassificacao;
        $rsListaNivel->proximo();
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    //MONTA O FORMULÁRIO DOS NIVEIS DE CLASSIFICAÇÃO:
    if ( count( $arCombosClassificacao ) ) {

        //CAMPO TEXT PARA A CHAVE DA CLASSIFICAÇÃO
        $obTxtChaveClassificacao = new TextBox;
        $obTxtChaveClassificacao->setName   ( "stChaveClassificacao" );
        $obTxtChaveClassificacao->setRotulo ( "Classificação" );
        $obTxtChaveClassificacao->setTitle  ( "Informe a classificação do item." );
        $obTxtChaveClassificacao->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveClassificacao->setSize      ( strlen($this->stMascara) + 2 );
        $obTxtChaveClassificacao->setNull      ( !$this->boClassificacaoRequerida );
        $obTxtChaveClassificacao->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveClassificacao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&inCodCatalogo='+document.frm.inCodCatalogo.value+'&inNumNiveisClassificacao='+document.frm.inNumNiveisClassificacao.value+'&stChaveClassificacao='+this.value,'preencheCombosClassificacao');".$this->stOnChangeCombo);
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveisClassificacao" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveClassificacao );
        foreach ($arCombosClassificacao as $obCmbClassificacao) {
            $obFormulario->addComponente( $obCmbClassificacao );
        }

    }
}

/**
    * Monta os combos de classificação conforme o nível setado
    * @access Public
    * @param Integer $inPosCombo Posição do combo no formulário
    * @param Integer $inNumCombos Número de combos no formulário
*/
function preencheProxComboClassificacao($inPosCombo, $inNumCombos)
{
     $js = isset($js) ? $js : null;
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo ( $this->inCodigoCatalogo );
    $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->addCatalogoNivel();
    $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel( $this->inCodigoNivel );
    $obErro = $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->listarNiveis( $rsListaNivel );
    for ($inCont = $inPosCombo; $inCont < $inNumCombos; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = $rsListaNivel->getCampo("descricao");
        $stNomeCombo = "inCodClassificacao_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione','', 'selected');\n";
    }
    if ($this->stCodEstruturalReduzido && $this->inCodigoNivel) {
        $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->addCatalogoNivel();
        $this->obRAlmoxarifadoClassificacao->setEstrutural( $this->stCodEstruturalReduzido );
        $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel    ( $this->inCodigoNivel +1   );
        $obErro = $this->obRAlmoxarifadoClassificacao->listarDetalhesClassificacao( $rsListaClassificacao );
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stCodEstruturalReduzido .= ".";
            while ( !$rsListaClassificacao->eof() ) {
                $stChaveClassificacao  = $rsListaClassificacao->getCampo( "nivel" )."-";
                $stChaveClassificacao .= $rsListaClassificacao->getCampo( "cod_classificacao")."-";
                $stChaveClassificacao .= $rsListaClassificacao->getCampo( "cod_estrutural")."-";
                $stChaveClassificacao .= $rsListaClassificacao->getCampo( "cod_nivel");
                $stNomClassificacao   = $rsListaClassificacao->getCampo( "descricao" );
                Sessao::write("nomFiltro['nom_classificacao'][".$rsListaClassificacao->getCampo( 'nivel' )."]" , $rsListaClassificacao->getCampo( "descricao" ));
                $js .= "f.inCodClassificacao_".$inPosCombo.".options[$inContador] = ";
                $js .= "new Option('".$stNomClassificacao."','".$stChaveClassificacao."',''); \n";
                $inContador++;
                $rsListaClassificacao->proximo();
            }
        }
    }
    $this->stCodEstrutural = $this->stCodEstruturalReduzido;
    $js .= "f.stChaveClassificacao.value = '".$this->stCodEstruturalReduzido."';\n";
    $this->obRAlmoxarifadoClassificacao->setCodigo ( "" );

    return $js;
}

/**
    * Preenche os combos a partir da chave da classificação
    * @access Public
*/
function preencheCombosClassificacao($inNumCombos)
{
    $js = isset($js) ? $js : null;
    $stSelecionado = "";
    $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo ( $this->inCodigoCatalogo );
    $obErro = $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->listarNiveis( $rsListaNivel );
    if ( strrpos($this->stCodEstruturalReduzido, ".")+1 == strlen( $this->stCodEstruturalReduzido) ) {
        $stCodEstruturalReduzido= substr( $this->stCodEstruturalReduzido, 0, strlen( $this->stCodEstruturalReduzido) - 1 );
    } else {
        $stCodEstruturalReduzido = $this->stCodEstruturalReduzido;
    }
    $arCodEstruturalReduzido = explode( ".", $stCodEstruturalReduzido);
    $stCodEstruturalReduzido = "";
    $stEstruturalRetorno     = "";
    $inCont = 1;//CONTADOR DOS COMBOS DOS NIVEIS DE CLASSIFICAÇÃO
    while ( !$rsListaNivel->eof() and key( $arCodEstruturalReduzido) < count( $arCodEstruturalReduzido) ) {

         if ($inCont == 1) {
             $stCodEstruturalReduzido .= current( $arCodEstruturalReduzido);
             $boMontaCombos = true;
         } else {
             $boMontaCombos = true;
             $stCodEstruturalReduzido .= ".".current( $arCodEstruturalReduzido);
         }
         next( $arCodEstruturalReduzido );
         $stNomeCombo = "inCodClassificacao_".$inCont++;
         $stSelecione = $rsListaNivel->getCampo("descricao");
         $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
         $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione','', 'selected');\n";
         if ($boMontaCombos) {
             $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->addCatalogoNivel();
             $this->obRAlmoxarifadoClassificacao->setRComboClassificacaoCompleta($this->getComboClassificacaoCompleta());
             $this->obRAlmoxarifadoClassificacao->setEstrutural($stCodEstruturalReduzido );
             $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel       ( $rsListaNivel->getCampo("nivel") );
             $obErro = $this->obRAlmoxarifadoClassificacao->listarDetalhesClassificacao ( $rsListaClassificacao );

             $inContador = 1;
             while ( !$rsListaClassificacao->eof() ) {
                 $stChaveClassificacao  = $rsListaClassificacao->getCampo( "nivel" )."-";
                 $stChaveClassificacao .= $rsListaClassificacao->getCampo( "cod_classificacao")."-";
                 $stChaveClassificacao .= $rsListaClassificacao->getCampo( "cod_estrutural")."-";
                 $stChaveClassificacao .= $rsListaClassificacao->getCampo( "cod_nivel");
                 $stNomClassificacao   = $rsListaClassificacao->getCampo( "descricao" );
                 Sessao::write("nomFiltro['nom_classificacao'][".$rsListaClassificacao->getCampo( 'nivel' )."]" , $rsListaClassificacao->getCampo( "descricao") );
                 $stReduzido = $rsListaClassificacao->getCampo( "cod_estrutural") ;
                 while( preg_match('/\.0+$/', $stReduzido ))
                     $stReduzido = preg_replace('/\.0+$/', '', $stReduzido );

                 if ( trim($stCodEstruturalReduzido) == trim($stReduzido) ) {
                      $stSelected           = "selected";
                      $stEstruturalRetorno  = $stReduzido;
                      $stSelecionado        = $stChaveClassificacao;
                 } else {
                        $stSelected = "";
                  }
                 $js .= "f.".$stNomeCombo.".options[$inContador] = ";
                 $js .= "new Option('".addslashes($stNomClassificacao)."','".$stChaveClassificacao."','".$stSelected."'); \n";
                 $inContador++;
                 $rsListaClassificacao->proximo();
             }
             $js .= "f.".$stNomeCombo.".value = '".$stSelecionado."' ;";
         }
         $rsListaNivel->proximo();
    }
    if ($stEstruturalRetorno) {
        if ($stEstruturalRetorno[strlen($stEstruturalRetorno)-1] != '.' && $this->stCodEstruturalReduzido[strlen($this->stCodEstruturalReduzido)-1] == '.') {
            $stEstruturalRetorno .= '.';
        }
    }

    $js .= "f.stChaveClassificacao.value = '".$stEstruturalRetorno."';\n";
    if ( trim($stEstruturalRetorno) <> trim($this->stCodEstruturalReduzido) ) {
        $js .= "alertaAviso(' Classificação ".$this->stCodEstruturalReduzido." não existe.','form','erro','".Sessao::getId()."','../');\n";
    }
    $js .= "f.stChaveClassificacao.focus();";

    return $js;
}

function geraFormularioReadOnly(&$obFormulario)
{
    global $pgOcul;

    $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo( $this->inCodigoCatalogo );
    $obErro = $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->listarNiveis( $rsListaNivel );

    $arCombosClassificacao = array();
    $boFlagPrimeiroNivel = true;
    $inContNomeLabel = 1;
    $this->obRAlmoxarifadoClassificacao->setCodigo($this->inCodigoClassificacao);
    $obErro = $this->obRAlmoxarifadoClassificacao->consultar();
    $obErro = $this->obRAlmoxarifadoClassificacao->listarClassificacao($rsRecordSet);
    while ( !$rsListaNivel->eof() ) {
        $inNumNivel = $rsListaNivel->getCampo( "nivel" );
        $stNomeNivel[$inNumNivel] = $rsListaNivel->getCampo( "descricao" );
        //DEFINICAO PADRAO DOS LABELS DE CLASSIFICAÇÃO
        $obLblClassificacao = new Label;
        $obLblClassificacao->setRotulo    ( "$stNomeNivel[$inNumNivel]"     );
        $obLblClassificacao->setName( "inCodClassificacao_".$inNumNivel );
        if ($rsRecordSet->getCampo('descricao')) {
            $obLblClassificacao->setValue($rsRecordSet->getCampo('descricao') );
        // } else {
        //     $obLblClassificacao->setValue($this->obRAlmoxarifadoClassificacao->getDescricao());
            $inContNomeLabel++;
            $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');
            $arLblClassificacao[] = $obLblClassificacao;
        }
        $rsListaNivel->proximo();
        $rsRecordSet->proximo();
    }

    //CAMPO LABEL PARA A CHAVE DA CLASSIFICAÇÃO
    $obLblChaveClassificacao = new Label;
    $obLblChaveClassificacao->setRotulo ( "Classificação" );
    //$obLblChaveClassificacao->setValue  ( $this->stCodEstruturalReduzido );
    $obLblChaveClassificacao->setValue  ( $stCodEstrutural );

    $obHdnChaveClassificacao = new Hidden;
    $obHdnChaveClassificacao->setName   ( "stChaveClassificacao" );
    $obHdnChaveClassificacao->setValue  ( $this->stCodEstruturalReduzido );

    //ADICIONA OS COMPONENTES NO FORMULARIO
    $obFormulario->addHidden     ( $obHdnChaveClassificacao );
    $obFormulario->addComponente ( $obLblChaveClassificacao );
    foreach ($arLblClassificacao as $obLblClassificacao) {
        $obFormulario->addComponente( $obLblClassificacao );
    }

}

}
?>
