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
* Classe de geração de componetes para cadastro de atividades
* Data de Criação: 31/05/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once '../../../includes/cabecalho.php';
include_once( CAM_REGRA."RAtividade.class.php"         );

/**
    * Classe  de geração de componetes para cadastro de atividades
    * Data de Criação   : 31/05/2004
    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/

class MontaAtividades extends Objeto
{
/**
    * @access Private
    * @var Integer
*/
var $obRAtividade;

/**
    * @access Private
    * @var Integer
*/
var $stChave;

/**
    * @access Private
    * @var Integer
*/
var $stNomeCampo;

/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivel;

/**
    * @access Private
    * @var Integer
*/
var $inCodigoVigencia;

/**
    * @access Private
    * @var Integer
*/
var $stCampoAlterado;

/**
    * @access Private
    * @var Integer
*/
var $stMascaraAtividade;

//SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setChave($valor) { $this->stChave            = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel      = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCampoAlterado($valor) { $this->stCampoAlterado    = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setMascaraAtividade($valor) { $this->stMascaraAtividade = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getChave() { return $this->stChave;            }

/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;      }

/**
    * @access Public
    * @return Integer
*/
function getCodigoVigencia() { return $this->inCodigoVigencia;   }

/**
    * @access Public
    * @return Integer
*/
function getCampoAlterado() { return $this->stCampoAlterado;    }

/**
    * @access Public
    * @return Integer
*/
function getMascaraAtividade() { return $this->stMascaraAtividade; }

/**
     * Método construtor
     * @access Public
*/
function MontaAtividades()
{
    $this->obRAtividade = new RAtividade;
    $this->stNomeCampo = "inCodigoAtividade_";
}

/**
     * FALTA DESCRICAO
     * @access Public
*/
function geraMascaraAtividade()
{
    //Monta a mascara para a chave dos combos dos niveis
    $this->obRAtividade->setCodigoVigencia  ( $this->getCodigoVigencia() );
    $this->obRAtividade->listarNiveis( $rsNiveis );
    $stMascaraAtividade = "";
    while ( !$rsNiveis->eof() ) {
        if ( $rsNiveis->getCampo( "cod_nivel" ) == $this->getCodigoNivel() ) {
            $stMascaraNivel =  $rsNiveis->getCampo( "mascara" );
            break;
        }
        $stMascaraAtividade .= $rsNiveis->getCampo( "mascara" ).".";
        $rsNiveis->proximo();
    }
    $this->stMascaraAtividade = substr( $stMascaraAtividade, 0, strlen( $stMascaraAtividade ) - 1 );
}

/**
     * FALTA DESCRICAO
     * @access Public
     * @param Object $obFormulario
*/
function geraFormulario(&$obFormulario)
{
    $this->geraMascaraAtividade();
    //Define objeto TEXT para armazenar a chave da ATIVIDADE
    $obTxtChaveAtividade = new textBox;
    $obTxtChaveAtividade->setName      ( "stChaveAtividade" );
    $obTxtChaveAtividade->setRotulo    ( "Nivel Superior"   );
    $obTxtChaveAtividade->setSize      ( strlen($this->stMascaraAtividade) );
    $obTxtChaveAtividade->setMaxLength ( strlen($this->stMascaraAtividade) );
    $obTxtChaveAtividade->setNull      ( false );
    $obTxtChaveAtividade->obEvento->setOnKeyUp( "mascaraDinamico('".$this->stMascaraAtividade."', this, event);");
    $obTxtChaveAtividade->obEvento->setOnChange( "montaCombosAtividades();" );
    $obTxtChaveAtividade->setValue    ( substr ( $this->stChave, 0, strrpos( $this->stChave, "." ) ) );

    //Define os objetos SELECT para armazenar as ATIVIDADES
    $inContNivelAtividade = 1;
    $arCmbNivelAtividade = array();
    while ( $inContNivelAtividade < $this->getCodigoNivel() ) {
        $stNomeCombo = $this->stNomeCampo.$inContNivelAtividade;
        $obCmbNivelAtividade = new select;
        $obCmbNivelAtividade->setName      ( $stNomeCombo                  );
        $obCmbNivelAtividade->setRotulo    ( "Nivel Superior"              );
        $obCmbNivelAtividade->setStyle     ( "width:250px"                 );
        $obCmbNivelAtividade->setCampoId   ( "[cod_atividade]-[cod_pai]"   );
        $obCmbNivelAtividade->setCampoDesc ( "nom_atividade"               );
        $obCmbNivelAtividade->addOption    ( "", "Selecione"               );
        if ($inContNivelAtividade == 1) {
            $this->obRAtividade->setCodigoNivel   ( $inContNivelAtividade     );
            $this->obRAtividade->setCodigoPai     ( $inContNivelAtividade - 1 );
            $this->obRAtividade->listarAtividades ( $rsAtividade              );
            $obCmbNivelAtividade->preencheCombo ( $rsAtividade );
        }
        $obCmbNivelAtividade->obEvento->setOnChange("buscaValoresAtividades( this.name );");
        $inContNivelAtividade++;
        if ( $inContNivelAtividade == $this->getCodigoNivel() ) {
            $obCmbNivelAtividade->setNull( false );
        }
        $arCmbNivelAtividade[] = $obCmbNivelAtividade;
    }

    //ADICIONA OS COMPONENTES NO FORMULARIO
    if ( $this->getCodigoNivel() > 1 ) {
        $obFormulario->addComponente( $obTxtChaveAtividade );
        foreach ($arCmbNivelAtividade as $obCmbNivelAtividade) {
            $obFormulario->addComponente( $obCmbNivelAtividade );
        }
    }
}//FIM  DA GERAFORMULARIO

/**
     * FALTA DESCRICAO
     * @access Public
     * @param Object $js
     *                   return Object
*/
function preencheCombos(&$js)
{
    $this->geraMascaraAtividade();
    $arCodigoAtividade = preg_split( "/[^a-zA-Z0-9]/", $this->stChave );
    $inContNiveis  = 1;
    $this->obRAtividade->setCodigoVigencia  ( $this->getCodigoVigencia() );
    $inCodSuperior = 0;
    foreach ($arCodigoAtividade as $inCodigoAtividade) {
        $this->obRAtividade->setCodigoNivel   ( $inContNiveis  );
        $this->obRAtividade->setCodigoPai     ( $inCodSuperior );
        $obErro = $this->obRAtividade->listarAtividades ( $rsAtividade   );
        if ( !$obErro->ocorreu() ) {
            $inCodSuperior = $inCodigoAtividade;//SETA O VALOR DO CODSUPERIOR PARA PROXIMA VOLTA DO LOOP
            $stNomeCampo = $this->stNomeCampo.$inContNiveis;
            //LIMPA O COMBO
            $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
            $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
            $inContador = 1;
            $boVerifica = true; //VERIFICA SE O CODIGO DA ATIVIDADE INFORMADA REALMENTE EXISTE
            while ( !$rsAtividade->eof() ) {
                $stSelected = "";
                $inCodAtividade = $rsAtividade->getCampo( "cod_atividade" );
                $inCodPai       = $rsAtividade->getCampo( "cod_pai" );
                $stNomAtividade = $rsAtividade->getCampo( "nom_atividade" );
                $stChave = $inCodAtividade."-".$inCodPai;
                if ( $inCodAtividade == (integer) $inCodigoAtividade ) {
                    $stSelected = "selected";
                    $boVerifica = false;//SE SETAR PRA FALSE E PQ EXISTE
                }
                $js .= "f.".$stNomeCampo.".options[$inContador] = new Option('".$stNomAtividade."','".$stChave."','".$stSelected."'); \n";
                $inContador++;
                $rsAtividade->proximo();
            }
            $inContNiveis++;
            if ($boVerifica) {
                break;
            }
        } else {
            break;
        }
    }
    if ( !$obErro->ocorreu() ) {
        if ($inContNiveis == 1) {
            $inContNiveis++;
        }
        //LIMPA OS COMBOS QUE ABAIXO DO ULTIMO NIVEL PREENCHIDO
        $inCodigoNivel = $this->getCodigoNivel() - 1 ;
        for ($inContNiveis; $inContNiveis <= $inCodigoNivel; $inContNiveis++) {
            $stNomeCampo = $this->stNomeCampo.$inContNiveis;
            $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
            $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
        }
    }

    return $obErro;
}

/**
     * FALTA DESCRICAO
     * @access Public
     * @param Object $js
*/
function preencheProxCombo(&$js)
{
    GLOBAL $_POST;
    $this->geraMascaraAtividade();
    $inCodNivel = substr( $this->stCampoAlterado, strlen($this->stNomeCampo) );
    $arCodPai = explode("-", $_POST[$this->stCampoAlterado]);
    $this->obRAtividade->setCodigoVigencia ( $this->getCodigoVigencia() );
    $this->obRAtividade->setCodigoNivel    ( $inCodNivel + 1 );
    $this->obRAtividade->setCodigoPai      ( $arCodPai[0] );
    $this->obRAtividade->listarAtividades  ( $rsAtividade   );
    $inProxNivel = $inCodNivel + 1;
    $stNomeCampo = $this->stNomeCampo.$inProxNivel;
    //PREENCHE O PROXIMO COMBO
    if ( $inProxNivel < $this->getCodigoNivel() ) {//VERIFICA SE JAH ESTA NO ULTIMO COMBO
        //LIMPA O PROX. COMBO
        $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
        $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        //PREENCHE O PROX. COMBO
        while ( !$rsAtividade->eof() ) {
            $inCodAtividade = $rsAtividade->getCampo( "cod_atividade" );
            $inCodPai       = $rsAtividade->getCampo( "cod_pai" );
            $stNomAtividade = $rsAtividade->getCampo( "nom_atividade" );
            $stChave = $inCodAtividade."-".$inCodPai;
            $js .= "f.".$stNomeCampo.".options[$inContador] = new Option('".$stNomAtividade."','".$stChave."',''); \n";
            $inContador++;
            $rsAtividade->proximo();
        }
    }
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    $inCodigoNivel = $this->getCodigoNivel() - 1 ;
    for ($inContNivel = $inCodNivel + 2 ; $inContNivel <= $inCodigoNivel; $inContNivel++) {
        $stNomeCampo = $this->stNomeCampo.$inContNivel;
        $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
        $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
    }
    //MONTA A CHAVE
    $arMascara = explode( ".", $this->stMascaraAtividade );
    $stChave = "";
    //RECUPERA O VALOR DOS COD. ANTERIORES AO NIVEL SELECIONADO
    for ($inCont = $inCodNivel; $inCont >= 1; $inCont--) {
        $arChaveCampo = explode("-",$_POST[$this->stNomeCampo.$inCont] );
        $stChave = str_pad( $arChaveCampo[0], strlen($arMascara[$inCont - 1]), "0", STR_PAD_LEFT).".".$stChave;
    }
    //RECUPERA O VALOR DOS COD. POSTERIORES AO NIVEL SELECIONADO
    for ($inCont = $inCodNivel + 1; $inCont <= $inCodigoNivel; $inCont++) {
        $stChave .= str_pad( "", strlen($arMascara[$inCont - 1]), "0", STR_PAD_LEFT).".";
    }
    //RETIRA O ULTIMO PONTO DA CHAVE MONTADA
    $stChave = substr( $stChave , 0, strlen( $stChave ) - 1 );

    $js .= "f.stChaveAtividade.value = '".$stChave."';\n";
}

}
?>
