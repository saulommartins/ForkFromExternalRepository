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
include_once( CAM_REGRA."RServico.class.php"         );

/**
    * Classe  de geração de componetes para cadastro de atividades
    * Data de Criação   : 10/06/2004
    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/

class MontaServicos extends Objeto
{
/**
    * @access Private
    * @var Object
*/
var $obRServico;

/**
    * @access Private
    * @var Object
*/
var $stChave;

/**
    * @access Private
    * @var Object
*/
var $stNomeCampo;

/**
    * @access Private
    * @var Object
*/
var $inCodigoNivel;

/**
    * @access Private
    * @var Object
*/
var $stCampoAlterado;

/**
    * @access Private
    * @var Object
*/
var $stMascaraServico;

/**
    * @access Private
    * @var Object
*/
var $inVigenciaServico;

/**
    * @access Private
    * @var Object
*/
var $inCodigoNivelServico;

/**
    * @access Private
    * @var Object
*/
var $stValorCampoAlterado;

/**
    * @access Private
    * @var Object
*/
var $boCadastroAtividade;

//SETTERS
/**
    * @access Public
    * @param String $valor
*/
function setRServico($valor) { $this->obRServico           = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setChave($valor) { $this->stChave              = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setNomeCampo($valor) { $this->stNomeCampo          = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCampoAlterado($valor) { $this->stCampoAlterado      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setMascaraServico($valor) { $this->stMascaraServico     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setVigenciaServico($valor) { $this->inVigenciaServico    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCodigoNivelServico($valor) { $this->inCodigoNivelServico = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setValorCampoAlterado($valor) { $this->stValorCampoAlterado = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCadastroAtividade($valor) { $this->boCadastroAtividade = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getRServico() { return $this->obRServico;           }

/**
    * @access Public
    * @return Integer
*/
function getChave() { return $this->stChave;              }

/**
    * @access Public
    * @return Integer
*/
function getNomeCampo() { return $this->stNomeCampo;          }

/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;        }

/**
    * @access Public
    * @return Integer
*/
function getCampoAlterado() { return $this->stCampoAlterado;      }

/**
    * @access Public
    * @return Integer
*/
function getMascaraServico() { return $this->stMascaraServico;     }

/**
    * @access Public
    * @return Integer
*/
function getVigenciaServico() { return $this->inVigenciaServico;    }

/**
    * @access Public
    * @return Integer
*/
function getCodigoNivelServico() { return $this->inCodigoNivelServico; }

/**
    * @access Public
    * @return Integer
*/
function getValorCampoAlterado() { return $this->stValorCampoAlterado; }

/**
    * @access Public
    * @return Integer
*/
function getCadastroAtividade() { return $this->boCadastroAtividade; }

/**
     * Método construtor
     * @access Private
*/
function MontaServicos()
{
    $this->obRServico = new RServico;
    $this->stNomeCampo = "inCodigoServico_";
    $this->boCadastroAtividade = false;
}

function geraMascaraServico()
{
    //Monta a mascara para a chave dos combos dos niveis
    $this->obRServico->setCodigoVigencia  ( $this->inVigenciaServico );
    $this->obRServico->listarNiveis( $rsNiveis );
    $stMascaraServico = "";
    while ( !$rsNiveis->eof() ) {
        if ( $rsNiveis->getCampo( "cod_nivel" ) == $this->inCodigoNivelServico and !$this->boCadastroAtividade ) {
            $stMascaraNivel =  $rsNiveis->getCampo( "mascara" );
            break;
        }
        $stMascaraServico .= $rsNiveis->getCampo( "mascara" ).".";
        $rsNiveis->proximo();
    }
    $this->stMascaraServico = substr( $stMascaraServico, 0, strlen( $stMascaraServico ) - 1 );
}

function geraFormulario(&$obFormulario)
{
    $this->geraMascaraServico();
    //Define objeto TEXT para armazenar a chave da ATIVIDADE
    $obTxtChaveServico = new textBox;
    $obTxtChaveServico->setName      ( "stChaveServico"                );
    $obTxtChaveServico->setRotulo    ( "*Serviço"                       );
    $obTxtChaveServico->setSize      ( strlen($this->stMascaraServico) );
    $obTxtChaveServico->setMaxLength ( strlen($this->stMascaraServico) );
    //$obTxtChaveServico->setNull      ( false                           );
    $obTxtChaveServico->obEvento->setOnKeyUp( "mascaraDinamico('".$this->stMascaraServico."', this, event);");
    $obTxtChaveServico->obEvento->setOnChange( "desabilitaIncluir();montaCombosServicos();" );

    $obHdnNomeServico = new Hidden;
    $obHdnNomeServico->setName  ( "stNomeServico" );
    $obHdnNomeServico->setValue ( ""              );

    //Define os objetos SELECT para armazenar as ATIVIDADES
    $inContNivelServico = 1;
    $arCmbNivelServico = array();
    if ($this->boCadastroAtividade) {
        $inCodigoNivelServico = $this->inCodigoNivelServico + 1;
    } else {
        $inCodigoNivelServico = $this->inCodigoNivelServico;
    }
    while ($inContNivelServico < $inCodigoNivelServico) {
        $stNomeCombo = $this->stNomeCampo.$inContNivelServico;
        $obCmbNivelServico = new select;
        $obCmbNivelServico->setName      ( $stNomeCombo              );
        $obCmbNivelServico->setRotulo    ( "*Serviço"                 );
        //$obCmbNivelServico->setNull      ( false                     );
        $obCmbNivelServico->setStyle     ( "width:250px"             );
        $obCmbNivelServico->setCampoId   ( "[cod_servico]-[cod_pai]" );
        $obCmbNivelServico->setCampoDesc ( "nom_servico"             );
        $obCmbNivelServico->addOption    ( "", "Selecione"           );
        if ($inContNivelServico == 1) {
            $this->obRServico->setCodigoNivel      ( $inContNivelServico     );
            $this->obRServico->setCodigoPaiServico ( $inContNivelServico - 1 );
            $this->obRServico->listarServicos      ( $rsServico              );
            $obCmbNivelServico->preencheCombo      ( $rsServico );
        }
        $obCmbNivelServico->obEvento->setOnChange("desabilitaIncluir();buscaValoresServicos( this.name );");
        $arCmbNivelServico[] = $obCmbNivelServico;
        $inContNivelServico++;
    }
    //ADICIONA OS COMPONENTES NO FORMULARIO
    if ($this->inCodigoNivelServico > 1) {
        $obFormulario->addComponente( $obTxtChaveServico );
        foreach ($arCmbNivelServico as $obCmbNivelServico) {
            $obFormulario->addComponente( $obCmbNivelServico );
        }
    }
    $obFormulario->addHidden( $obHdnNomeServico );
}//FIM  DA GERAFORMULARIO

function preencheCombos()
{
    $this->geraMascaraServico();
    $arCodigoServico = preg_split( "/[^a-zA-Z0-9]/", $this->stChave );
    $inContNiveis  = 1;
    $this->obRServico->setCodigoVigencia  ( $this->inVigenciaServico );
    if ($this->boCadastroAtividade) {
        $inCodigoNivelServico = $this->inCodigoNivelServico;
    } else {
        $inCodigoNivelServico = $this->inCodigoNivelServico - 1;
    }
    $inCodSuperior = 0;
    foreach ($arCodigoServico as $inCodigoServico) {
        $this->obRServico->setCodigoNivel      ( $inContNiveis  );
        $this->obRServico->setCodigoPaiServico ( $inCodSuperior );
        $this->obRServico->listarServicos      ( $rsServico     );
        $inCodSuperior = $inCodigoServico;//SETA O VALOR DO CODSUPERIOR PARA PROXIMA VOLTA DO LOOP
        $stNomeCampo = $this->stNomeCampo.$inContNiveis;
        //LIMPA O COMBO
        $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
        $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        $boVerifica = true; //VERIFICA SE O CODIGO DA ATIVIDADE INFORMADA REALMENTE EXISTE
        while ( !$rsServico->eof() ) {
            $stSelected = "";
            $inCodServico = $rsServico->getCampo( "cod_servico" );
            $inCodPai     = $rsServico->getCampo( "cod_pai"     );
            $stNomServico = $rsServico->getCampo( "nom_servico" );
            $stChave = $inCodServico."-".$inCodPai;
            if ( $inCodServico == (integer) $inCodigoServico ) {
                $stSelected = "selected";
                $boVerifica = false;//SE SETAR PRA FALSE E PQ EXISTE
                if ( $inContNiveis == ( $inCodigoNivelServico ) ) {
                    $js .= "f.stNomeServico.value = '".$stNomServico."';\n";
                }
            }
            $js .= "f.".$stNomeCampo.".options[$inContador] = new Option('".$stNomServico."','".$stChave."','".$stSelected."'); \n";
            $inContador++;
            $rsServico->proximo();
        }
        $inContNiveis++;
        if ($boVerifica) {
            break;
        }
    }
    if ($inContNiveis == 1) {
        $inContNiveis++;
    }
    //LIMPA OS COMBOS QUE ABAIXO DO ULTIMO NIVEL PREENCHIDO
    for ($inContNiveis; $inContNiveis <= $inCodigoNivelServico; $inContNiveis++) {
        $stNomeCampo = $this->stNomeCampo.$inContNiveis;
        $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
        $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
    }
    if ($this->boCadastroAtividade) {
        if ($boVerifica) {
            $js .= "f.btnIncluirServico.disabled = true;\n";
        } else {
            $js .= "f.btnIncluirServico.disabled = false;\n";
        }
    }

    executaFrameOculto($js);
}

function preencheProxCombo()
{
    $this->geraMascaraServico();
    $inCodNivel = substr( $this->stCampoAlterado, strlen($this->stNomeCampo) );
    $arCodPai = explode("-", $this->stValorCampoAlterado );
    $inProxNivel = $inCodNivel + 1;
    $stNomeCampo = $this->stNomeCampo.$inProxNivel;

    //PREENCHE O PROXIMO COMBO
    if ($this->boCadastroAtividade) {
        $inCodigoNivelServico = $this->inCodigoNivelServico + 1;
    } else {
        $inCodigoNivelServico = $this->inCodigoNivelServico;
    }
    if ($inProxNivel < $inCodigoNivelServico) {//VERIFICA SE JAH ESTA NO ULTIMO COMBO
        $this->obRServico->setCodigoVigencia   ( $this->inVigenciaServico );
        $this->obRServico->setCodigoNivel      ( $inCodNivel + 1          );
        $this->obRServico->setCodigoPaiServico ( $arCodPai[0]             );
        $this->obRServico->listarServicos      ( $rsServico               );
        //LIMPA O PROX. COMBO
        $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
        $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        //PREENCHE O PROX. COMBO
        while ( !$rsServico->eof() ) {
            $inCodServico = $rsServico->getCampo( "cod_servico" );
            $inCodPai       = $rsServico->getCampo( "cod_pai" );
            $stNomServico = $rsServico->getCampo( "nom_servico" );
            $stChave = $inCodServico."-".$inCodPai;
            $js .= "f.".$stNomeCampo.".options[$inContador] = new Option('".$stNomServico."','".$stChave."',''); \n";
            $inContador++;
            $rsServico->proximo();
        }
    } else {
        $this->obRServico->setCodigoServico    ( $arCodPai[0]             );
        $this->obRServico->setCodigoVigencia   ( $this->inVigenciaServico );
        $this->obRServico->setCodigoNivel      ( $inCodNivel              );
        $this->obRServico->setCodigoPaiServico ( $arCodPai[1]             );
        $this->obRServico->consultarServico();
        $js .= "f.stNomeServico.value = '".$this->obRServico->getDescricao()."';\n";
        if ( $this->boCadastroAtividade and $this->obRServico->getDescricao() ) {
            $js .= "f.btnIncluirServico.disabled = false;\n";
        }
    }
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    if ($this->boCadastroAtividade) {
        $inCodigoNivelServico = $this->inCodigoNivelServico;
    } else {
        $inCodigoNivelServico = $this->inCodigoNivelServico - 1;
    }
    for ($inContNivel = $inCodNivel + 2 ; $inContNivel <= $inCodigoNivelServico; $inContNivel++) {
        $stNomeCampo = $this->stNomeCampo.$inContNivel;
        $js .= "limpaSelect(f.".$stNomeCampo.",0); \n";
        $js .= "f.".$stNomeCampo.".options[0] = new Option('Selecione','', 'selected');\n";
    }
    //MONTA A CHAVE
    $arMascara = explode( ".", $this->stMascaraServico );
    $stChave = "";
    //RECUPERA O VALOR DOS COD. ANTERIORES AO NIVEL SELECIONADO
    for ($inCont = $inCodNivel; $inCont >= 1; $inCont--) {
        $arChaveCampo = explode("-",$_POST[$this->stNomeCampo.$inCont] );
        $stChave = str_pad( $arChaveCampo[0], strlen($arMascara[$inCont - 1]), "0", STR_PAD_LEFT).".".$stChave;
    }
    //RECUPERA O VALOR DOS COD. POSTERIORES AO NIVEL SELECIONADO
    for ($inCont = $inCodNivel + 1; $inCont <= $inCodigoNivelServico; $inCont++) {
        $stChave .= str_pad( "", strlen($arMascara[$inCont - 1]), "0", STR_PAD_LEFT).".";
    }
    //RETIRA O ULTIMO PONTO DA CHAVE MONTADA
    $stChave = substr( $stChave , 0, strlen( $stChave ) - 1 );

    $js .= "f.stChaveServico.value = '".$stChave."';\n";
    executaFrameOculto($js);
}

function montaListaServico($rsListaServico)
{
    if ( !$rsListaServico->eof() ) {
        $obListaServico = new Lista;
        $obListaServico->setMostraPaginacao( false );
        $obListaServico->setRecordSet( $rsListaServico );
        $obListaServico->addCabecalho();
        $obListaServico->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaServico->ultimoCabecalho->setWidth( 5 );
        $obListaServico->commitCabecalho();
        $obListaServico->addCabecalho();
        $obListaServico->ultimoCabecalho->addConteudo("Código ");
        $obListaServico->ultimoCabecalho->setWidth( 5 );
        $obListaServico->commitCabecalho();
        $obListaServico->addCabecalho();
        $obListaServico->ultimoCabecalho->addConteudo( "Nome do Serviço" );
        $obListaServico->ultimoCabecalho->setWidth( 35 );
        $obListaServico->commitCabecalho();
        $obListaServico->addCabecalho();
        $obListaServico->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaServico->ultimoCabecalho->setWidth( 5 );
        $obListaServico->commitCabecalho();

        $obListaServico->addDado();
        $obListaServico->ultimoDado->setAlinhamento( "DIREITA" );
        $obListaServico->ultimoDado->setCampo( "hierarquia_servico" );
        $obListaServico->commitDado();
        $obListaServico->addDado();
        $obListaServico->ultimoDado->setCampo( "nom_servico" );
        $obListaServico->commitDado();
        $obListaServico->addAcao();
        $obListaServico->ultimaAcao->setAcao( "EXCLUIR" );
        $obListaServico->ultimaAcao->setFuncao( true );
        $obListaServico->ultimaAcao->setLink( "JavaScript:excluiServico();" );
        $obListaServico->ultimaAcao->addCampo("1","inIndice");
        $obListaServico->commitAcao();

        $obListaServico->montaHTML();
        $stHTML =  $obListaServico->getHtml();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = "&nbsp;";
    }

    return $stHTML;
}

}
?>
