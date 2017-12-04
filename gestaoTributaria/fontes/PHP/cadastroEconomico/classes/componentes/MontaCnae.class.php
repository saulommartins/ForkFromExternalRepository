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

* $Id: MontaCnae.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.6  2007/05/17 21:12:09  cercato
Bug #9273#

Revision 1.5  2006/09/15 11:57:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

class MontaCnae extends Objeto
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoVigencia;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivel;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoCnae;
/**
    * @access Private
    * @var String
*/
var $stMascara;
/**
    * @access Private
    * @var String
*/
var $stValorComposto;
/**
    * @access Private
    * @var String
*/
var $stValorReduzido;
/**
    * @access Private
    * @var Object
*/
var $obRCEMCnae;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel = $valor;    }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoCnae($valor) { $this->inCodigoCnae = $valor;         }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido = $valor;  }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroCnae($valor) { $this->boCadastroCnae = $valor;       }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPopUp($valor) { $this->boPopUp        = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoVigenciaCnae() { return $this->inCodigoVigenciaCnae;    }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;       }
/**
    * @access Public
    * @return Integer
*/
function getCodigoCnae() { return $this->inCodigoCnae; }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;           }
/**
    * @access Public
    * @return String
*/
function getValorComposto() { return $this->stValorComposto;     }
/**
    * @access Public
    * @return String
*/
function getValorReduzido() { return $this->stValorComposto;     }
/**
    * @access Public
    * @return Boolean
*/
function getCadastroCnae() { return $this->boCadastroCnae;   }
/**
    * @access Public
    * @return Boolean
*/
function getPopUp() { return $this->boPopUp;  }

/**
     * Método construtor
     * @access Private
*/
function MontaCnae()
{
    $this->obRCEMCnae = new RCEMCnae( new RCEMAtividade );
    $this->stMascara = "";
    $this->boCadastroCnae = true;
    $this->boPopUp = false;
}

/**
    * Monta os combos de cnae conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigenciaCnae );
    if ($this->boCadastroCnae) {
        $this->obRCEMCnae->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMCnae->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    }
    $arCombosCnae = array();
    $boFlagPrimeiroNivelCnae = true;
    $inContNomeCombo = 1;
    while ( !$rsListaNivel->eof() ) {
        $stNumNivel = $rsListaNivel->getCampo("cod_nivel");
        $stNomeNivel[$stNumNivel] = $rsListaNivel->getCampo("nom_nivel");
        //DEFINICAO PADRAO DOS COMBOS DE cnae
        $obCmbCnae = new Select;
        $obCmbCnae->setRotulo    ( "CNAE"     );
        $obCmbCnae->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbCnae->setCampoId   ( "[cod_nivel],[cod_cnae],[valor],[valor_reduzido]" );
        $obCmbCnae->setCampoDesc ( "nom_atividade" );
        $obCmbCnae->setStyle     ( "width:350px"     );
        if ( $this->getCadastroCnae() ) {
            $obCmbCnae->setNull      ( false             );
        }
        $obCmbCnae->setName( "inCodCnae_".$inContNomeCombo++ );
        $obCmbCnae->obEvento->setOnChange( "preencheProxComboCnae( $inContNomeCombo );" );
        //PREENCHE APENAS O PRIMEIRO NIVEL
        if ($boFlagPrimeiroNivelCnae) {
           $boFlagPrimeiroNivelCnae = false;
           $this->obRCEMCnae->setCodigoNivel ( $rsListaNivel->getCampo("cod_nivel") );
           $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
           $obCmbCnae->preencheCombo( $rsListaCnae );
        }

        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara");
        if ( $stNumNivel < 3 )
            $this->stMascara .= '.';

        $arCombosCnae[] = $obCmbCnae;
        $rsListaNivel->proximo();
    }

    $this->stMascara = "Z".substr( $this->stMascara, 1, strlen($this->stMascara) );

    //MONTA O FORMULÁRIO DOS NIVEIS DE cnae:
    if ( count( $arCombosCnae ) ) {
        //CAMPO TEXT PARA A CHAVE DA cnae
        $obTxtChaveCnae = new TextBox;
        $obTxtChaveCnae->setName      ( "stChaveCnae" );
        $obTxtChaveCnae->setRotulo    ( "CNAE" );
        $obTxtChaveCnae->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveCnae->setSize      ( strlen($this->stMascara) + 2 );
        if ($this->boCadastroCnae) {
            $obTxtChaveCnae->setNull      ( false                    );
        }
        $obTxtChaveCnae->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveCnae->obEvento->setOnChange("preencheCombosCnae();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveisCnae" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveCnae );
        foreach ($arCombosCnae as $obCmbCnae) {
            $obFormulario->addComponente( $obCmbCnae );
        }
    }
}

/**
    * Monta os combos de cnae preenchidos conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormularioPreenchido(&$obFormulario)
{
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigenciaCnae );
    $stTemp = $this->stValorComposto;
    $this->stValorComposto = "";
    $this->geraFormulario( $obFormulario );
    $this->stValorReduzido = $stTemp;

    $this->preencheCombos2();
}

/**
    * Monta os combos de cnae conforme o nível setado
    * @access Public
    * @param Integer $inPosCombo Posição do combo no formulário
    * @param Integer $inNumCombos Número de combos no formulário
*/
function preencheProxCombo($inPosCombo, $inNumCombos)
{
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigencia );
    $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    for ($inCont = $inPosCombo; $inCont < $inNumCombos; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = "Selecione ".$rsListaNivel->getCampo("nom_nivel");
        $stNomeCombo = "inCodCnae_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('".$stSelecione."','', 'selected');\n";
    }
    if ($this->stValorReduzido) {
        $this->obRCEMCnae->setCodigoNivel      ( $this->inCodigoNivel    );
        $this->obRCEMCnae->setCodigoVigencia   ( $this->inCodigoVigenciaCnae );
        $this->obRCEMCnae->recuperaProximoNivel(  $rsProximoNivelCnae );
        $this->obRCEMCnae->setCodigoNivel      (  $rsProximoNivelCnae->getCampo("cod_nivel") );
        $this->obRCEMCnae->setValorReduzidoCnae( $this->stValorReduzido );
        $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stValorReduzido .= ".";
            while ( !$rsListaCnae->eof() ) {
                $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
                $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
                $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";
                $inContador++;
                $rsListaCnae->proximo();
            }
        }
    }
    $js .= "f.stChaveCnae.value = '".$this->stValorReduzido."';\n";
    $this->obRCEMCnae->setCodigoCnae ( "" );
    if ($this->boPopUp) {
        executaIFrameOculto ( $js );
    } else {
        sistemaLegado::executaFrameOculto ( $js );
    }
}

function preencheCombos2()
{
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigencia );
    $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    for ($inCont = 1; $inCont < 6; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = "Selecione ".$rsListaNivel->getCampo("nom_nivel");
        $stNomeCombo = "inCodCnae_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('".$stSelecione."','', 'selected');\n";
    }

    $stValorReduzido = $this->stValorReduzido;
    $arValorReduzido = explode( ".", $stValorReduzido );
    $stTMP = $arValorReduzido[2];
    $arValorReduzido[2] = substr( $stTMP, 0, 1 );
    $arValorReduzido[3] = substr( $stTMP, 1, 3 );
    $arValorReduzido[4] = substr( $stTMP, 5, 2 );

    $stValorSelecionar = -1;
    if ($arValorReduzido[0] != "") {
        $this->obRCEMCnae->setCodigoNivel ( 1 );
        $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0] );
        $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
        $stValorSelecionar = $rsListaCnae->getCampo( "cod_cnae");
    }

    $this->obRCEMCnae->setCodigoNivel ( 1 );
    $this->obRCEMCnae->setValorReduzidoCnae( "" );
    $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );

    $inContador = 1;
    $inPosCombo = 1;
    while ( !$rsListaCnae->Eof() ) {
        $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
        $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
        $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
        $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
        $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
        $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
        if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_cnae") )
            $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
        else
            $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

        $inContador++;
        $rsListaCnae->proximo();
    }

    if ($arValorReduzido[0] != "") {
        $stValorSelecionar = -1;
        if ($arValorReduzido[1] != "") {
            $this->obRCEMCnae->setCodigoNivel ( 2 );
            $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0].".".$arValorReduzido[1] );
            $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
            $stValorSelecionar = $rsListaCnae->getCampo( "cod_cnae");
        }

        $this->obRCEMCnae->setCodigoNivel ( 2 );
        $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0] );
        $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
        $inContador = 1;
        $inPosCombo = 2;
        while ( !$rsListaCnae->Eof() ) {
            $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
            $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
            $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
            $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
            $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
            $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
            if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_cnae") )
                $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
            else
                $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

            $inContador++;
            $rsListaCnae->proximo();
        }

        if ($arValorReduzido[1] != "") {
            $stValorSelecionar = -1;
            if ($arValorReduzido[2] != "") {
                $this->obRCEMCnae->setCodigoNivel ( 3 );
                $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2] );
                $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
                $stValorSelecionar = $rsListaCnae->getCampo( "cod_cnae");
            }

            $this->obRCEMCnae->setCodigoNivel ( 3 );
            $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0].".".$arValorReduzido[1] );
            $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
            $inContador = 1;
            $inPosCombo = 3;
            while ( !$rsListaCnae->Eof() ) {
                $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
                $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
                $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_cnae") )
                    $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
                else
                    $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

                $inContador++;
                $rsListaCnae->proximo();
            }

            if ($arValorReduzido[2] != "") {
                $stValorSelecionar = -1;
                if ($arValorReduzido[3] != "") {
                    $this->obRCEMCnae->setCodigoNivel ( 4 );
                    $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2].$arValorReduzido[3] );
                    $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
                    $stValorSelecionar = $rsListaCnae->getCampo( "cod_cnae");
                }

                $this->obRCEMCnae->setCodigoNivel ( 4 );
                $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2] );
                $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
            //C.14.22-3/00
                $inContador = 1;
                $inPosCombo = 4;
                while ( !$rsListaCnae->Eof() ) {
                    $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
                    $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
                    $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
                    $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
                    $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                    $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                    if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_cnae") )
                        $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
                    else
                        $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

                    $inContador++;
                    $rsListaCnae->proximo();
                }

                if ($arValorReduzido[3] != "") {
                    $stValorSelecionar = -1;
                    if ($arValorReduzido[4] != "") {
                        $this->obRCEMCnae->setCodigoNivel ( 5 );
                        $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2].$arValorReduzido[3].'/'.$arValorReduzido[4] );
                        $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
                        $stValorSelecionar = $rsListaCnae->getCampo( "cod_cnae");
                    }

                    $this->obRCEMCnae->setCodigoNivel ( 5 );
                    $this->obRCEMCnae->setValorReduzidoCnae( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2].$arValorReduzido[3] );
                    $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
                //C.14.22-3/00
                    $inContador = 1;
                    $inPosCombo = 5;
                    while ( !$rsListaCnae->Eof() ) {
                        $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
                        $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
                        $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
                        $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
                        $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                        $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                        if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_cnae") )
                            $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
                        else
                            $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

                        $inContador++;
                        $rsListaCnae->proximo();
                    }
                }
            }
        }
    }

    $js .= "f.stChaveCnae.value = '".$this->stValorReduzido."';\n";

    sistemaLegado::executaFrameOculto ( $js );
}

/**
    * Preenche os combos a partir da chave da cnae
    * @access Public
*/
function preencheCombos()
{
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigencia );
    if ($this->boCadastroCnae) {
        $this->obRCEMCnae->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMCnae->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    }
    if ( strrpos($this->stValorReduzido, ".") == strlen( $this->stValorReduzido ) ) {
        $stValorReduzido = substr( $this->stValorReduzido , 0, strlen( $this->stValorReduzido ) - 1 );
    } else {
        $stValorReduzido = $this->stValorReduzido;
    }
    $arValorReduzido = explode( ".", $stValorReduzido );
    $stValorReduzido = "";
    $inCont = 1;//CONTADOR DOS COMBOS DOS NIVEIS DE SERVCO

    while ( !$rsListaNivel->eof() and key( $arValorReduzido ) < count( $arValorReduzido ) ) {
         if ($inCont == 1) {
             $stValorReduzido .= current( $arValorReduzido );
             $boMontaCombosCnae = true;
         } else {
             if ( $this->obRCEMCnae->getValorReduzido() ) {
                 $boMontaCombosCnae = true;
             } else {
                 $boMontaCombosCnae = false;
             }
             $stValorReduzido .= ".".current( $arValorReduzido );
         }
         next( $arValorReduzido );
         $stNomeCombo = "inCodCnae_".$inCont++;
         $stSelecione = $rsListaNivel->getCampo("nom_nivel");
         $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
         $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
         if ($boMontaCombosCnae) {
             $this->obRCEMCnae->setCodigoNivel       ( $rsListaNivel->getCampo("cod_nivel") );
             $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
             $this->obRCEMCnae->setValorReduzidoCnae( $stValorReduzido );
             $inContador = 1;
             while ( !$rsListaCnae->eof() ) {
                 $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
                 $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
                 $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
                 $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
                 $stNomeCnae   = $rsListaCnae->getCampo( "nom_atividade" );
                 if ( $rsListaCnae->getCampo( "valor_reduzido") == $stValorReduzido ) {
                     $stSelected = "selected";
                 } else {
                     $stSelected = "";
                 }
                 $js .= "f.".$stNomeCombo.".options[$inContador] = ";
                 $js .= "new Option('".$stNomeCnae."','".$stChaveCnae."','".$stSelected."'); \n";
                 $inContador++;
                 $rsListaCnae->proximo();
             }
         }
         $rsListaNivel->proximo();
    }
    if ($this->boPopUp) {
        executaIFrameOculto( $js );
    } else {
        sistemaLegado::executaFrameOculto ( $js );
    }
}

}
?>
