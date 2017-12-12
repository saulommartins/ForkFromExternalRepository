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
/*
 * Titulo do arquivo : Componente Organograma
 * Data de Criação   : 28/11/2008

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 $Id:$
*/

class IMontaOrganograma extends Objeto
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivel;
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
    * @var Boolean
*/
var $boCadastroOrganograma;
/**
    * @access Private
    * @var Boolean
*/
var $boRetornaJs;
/**
    * @access Private
    * @var Boolean
*/
var $boNullBarra;
/**
    *@access Private
    *@var String
*/
var $stDefinicao;
/**
    *@access Private
    *@var Array
*/
var $arCmbOrganograma;
/**
    *@access Private
    *@var Integer
*/
var $inNivelObrigatorio;
/**
    *@access Private
    *@var Boolean
*/
var $boMostraComboOrganograma;
/**
    *@access Private
    *@var Integer
*/
var $inNumNiveis;
/**
    *@access Private
    *@var Boolean
*/
var $boMostraClassificacao;
/**
    *@access Private
    *@var String
*/
var $stIdOrganograma;
/**
    *@access Private
    *@var Integer
*/
var $inCodOrgao;
/**
    *@access Private
    *@var Integer
*/
var $stCodClassificacao;

/**
    *@access Private
    *@var Integer
*/
var $stHdnUltimoOrgaoSelecionado;

/**
    *@access Private
    *@var Integer
*/
var $stComponenteLeitura;

/**
    *@access Private
    *@var Integer
*/
var $arLblOrganograma;

/**
    *@access Private
    *@array mixed
*/
var $stHiddenInformacoes;

/**
    *@access Private
    *@string
*/
var $stStyle;

/**
    *@access Private
    *@boolean
*/
var $stEscondeHiddenCombos;

/**
    *@access Private
    *@boolean
*/
var $stRotuloComboOrganograma;

/**
    *@access Private
    *@string
*/
var $stTitle;

/**
    *@access Private
    *@string
*/
var $stHiddenEvalName;

function setCodigoNivel($valor) { $this->inCodigoNivel       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido     = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroOrganograma($valor) { $this->boCadastroOrganograma = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setRetornaJs($valor) { $this->boRetornaJs = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setNullBarra($valor) { $this->boNullBarra = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setScript($valor) { $this->stScript = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/

function setNivelObrigatorio($valor) { $this->inNivelObrigatorio = $valor;$this->setCadastroOrganograma(true); }

/**
    * @access Public
    * @param Boolean $valor
*/
function setMostraComboOrganograma($valor) { $this->boMostraComboOrganograma = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumNiveis($valor) { $this->inNumNiveis = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setMostraUltimoNivel($valor) { $this->obROrganograma->setMostraUltimoNivel($valor); }
/**
    * @access Public
    * @param Boolean $valor
*/
function setMostraClassificacao($valor) { $this->boMostraClassificacao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setIdOrganograma($valor) { $this->stIdOrganograma = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrgao($valor) { $this->inCodOrgao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodClassificacao($valor) { $this->stCodClassificacao = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setRotuloComboOrganograma($valor) { $this->stRotuloComboOrganograma = $valor; }

/**
*
*@access public
*@param integer
*
*/
function setUltimoOrgaoSelecionado($valor) { $this->stHdnUltimoOrgaoSelecionado = $valor;}

/**
    * @access Public
    * @return Integer
*/
function setComponenteSomenteLeitura($valor) { $this->stComponenteLeitura = $valor;}

/**
    * @access Public
    * @return Integer
*/
function setHiddenInformacoes($valor) { $this->stHiddenInformacoes = $valor;}

/**
    * @access Public
    * @return Integer
*/
function setStyle($valor) { $this->stStyle = $valor;}

/**
    * @access Public
    * @param string $valor
*/
function setHiddenEvalName($valor) {$this->stHiddenEvalName = $valor;}

/**
    * @access Public
    * @return Integer
*/
function getTitle() { return $this->stTitle;  }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;     }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;         }
/**
    * @access Public
    * @return String
*/
function getValorComposto() { return $this->stValorComposto;   }
/**
    * @access Public
    * @return String
*/
function getValorReduzido() { return $this->stValorReduzido;   }
/**
    * @access Public
    * @return Boolean
*/
function getCadastroOrganograma() { return $this->boCadastroOrganograma = $valor; }
/**
    * @access Public
    * @return Boolean
*/
function getRetornaJs() { return $this->boRetornaJs; }
/**
    * @access Public
    * @return Boolean
*/
function getNullBarra() { return $this->boNullBarra; }
/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao; }
/**
    * @access Public
    * @return Array
*/
function getCmbOrganograma() { return $this->arCmbOrganograma; }
/**
    * @access Public
    * @return Boolean
*/
function getMostraComboOrganograma() { return $this->boMostraComboOrganograma; }
/**
    * @access Public
    * @return Integer
*/
function getNumNiveis() { return $this->inNumNiveis; }
/**
    * @access Public
    * @return Boolean
*/
function getMostraUltimoNivel() { return $this->obROrganograma->getMostraUltimoNivel(); }
/**
    * @access Public
    * @return Boolean
*/
function getMostraClassificacao() { return $this->boMostraClassificacao; }
/**
    * @access Public
    * @return String
*/
function getIdOrganograma() { return $this->stIdOrganograma; }
/**
    * @access Public
    * @return Integer
*/
function getCodOrgao() { return $this->inCodOrgao; }
/**
    * @access Public
    * @return String
*/
function getCodClassificacao() { return $this->stCodClassificacao; }

/**
    * @access Public
    * @return String
*/
function getScript() { return $this->stScript; }

/**
*
*@access public
*@return string
*
*/
function getUltimoOrgaoSelecionado() { return $this->stHdnUltimoOrgaoSelecionado;}

/**
*
*@access public
*@return integer
*
*/
function getNivelObrigatorio() { return $this->inNivelObrigatorio;}

/**
    * @access Public
    * @return Integer
*/
function getComponenteSomenteLeitura() { return $this->stComponenteLeitura;}

/**
    * @access Public
    * @return Integer
*/
function getHiddenInformacoes() { return $this->stHiddenInformacoes;}

/**
    * @access Public
    * @return Integer
*/
function getStyle() {return $this->stStyle;}

/**
    * @access Public
    * @return string
*/
function getHiddenEvalName() {return $this->stHiddenEvalName;}

/**
    * @access Public
    * @param String $valor
*/
function getRotuloComboOrganograma() { return $this->stRotuloComboOrganograma; }

/**
     * Método construtor
     * @access Private
     * @param Boolean $boMostraComboOrganograma mostra uma combo com os organogramas existentes na base
     * @param Integer $inCodigoNivel            define até qual nível do organograma o sistema deve considerar
*/
function IMontaOrganograma($boMostraComboOrganograma = false, $inCodigoNivel = -1)
{
    include_once CAM_GA_ORGAN_NEGOCIO.'ROrganogramaOrganograma.class.php';
    $this->obROrganograma = new ROrganogramaOrganograma;
    $this->stMascara = "";
    $this->boRetornaJs = false;
    $this->setDefinicao("ORGANOGRAMA");
    $this->arCmbOrganograma = array();
    $this->boMostraClassificacao = true;
    $this->setComponenteSomenteLeitura(false);
    $this->boMostraComboOrganograma = $boMostraComboOrganograma;
    $this->inCodigoNivel = $inCodigoNivel;
    $this->setStyle('');
    $this->setRotuloComboOrganograma('Organograma');
    $this->stIdOrganograma = 'inCodOrganograma';
}

/**
    * Monta os combos para o formulário
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    Sessao::remove($this->stIdOrganograma.'mascaraOrganograma');

    if ($this->inCodOrgao) {
        $stCampo = 'cod_organograma';
        $stTabela = 'organograma.orgao_nivel';
        $stFiltro = ' WHERE cod_orgao = '.$this->inCodOrgao.' LIMIT 1 ';
        $inCodOrganograma = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
        $this->obROrganograma->setCodOrganograma($inCodOrganograma);

        if ($inCodOrganograma != '') {
            $stFiltroOrgao  = ' WHERE tabela.cod_orgao = '.$this->inCodOrgao;
            $stFiltroOrgao .= '   AND tabela.cod_organograma = '.$inCodOrganograma;
            $stFiltroOrgao .= ' LIMIT 1';
            $this->obROrganograma->recuperaClassificacaoOrgao($rsClassificacao, $stFiltroOrgao);

            $this->stCodClassificacao = $rsClassificacao->getCampo('orgao_reduzido');
        }
    }

    // Se não é para mostrar a combo dos organogramas, monta somente as combos dos órgãos
    if (!$this->boMostraComboOrganograma) {
        if ($this->getComponenteSomenteLeitura() == false) {
            $this->montaCombos($obFormulario);
        } else {
            $this->montaLabels($obFormulario);
        }
    } else {
        if ($this->getComponenteSomenteLeitura() == false) {
            // monta somente a combo dos organogramas
            $this->montaComboOrganograma($obFormulario);
        } else {
            $this->montaLabelOrganograma($obFormulario);
        }
    }
}

/**
    * Monta a combo dos organogramas existentes na base
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function montaComboOrganograma(&$obFormulario)
{
    $inCodOrganograma = $this->obROrganograma->getCodOrganograma();

    # Lista todos os organogramas
    $this->obROrganograma->setCodOrganograma('');
    $obErro = $this->obROrganograma->listar($rsListaOrganograma);

    # Caso tenha um organograma setado (edição)
    $this->obROrganograma->setCodOrganograma($inCodOrganograma);

    # Definição do combo dos organogramas
    $obCmbOrganograma = new Select;

    if ($this->getNullBarra()) {
        $obCmbOrganograma->setNullBarra(true);
    }

    $obCmbOrganograma->setRotulo   ($this->getRotuloComboOrganograma());
    $obCmbOrganograma->addOption   ("", "Selecione");
    $obCmbOrganograma->setCampoId  ("[cod_organograma]§[implantacao]§[cod_norma]");
    $obCmbOrganograma->setCampoDesc("[cod_organograma] - [implantacao]");
    $obCmbOrganograma->setStyle    ($this->getStyle());

    if ($this->boCadastroOrganograma) {
        $obCmbOrganograma->setNull(false);
    }

    $obCmbOrganograma->setName($this->stIdOrganograma.'Organograma');
    $obCmbOrganograma->setId  ($this->stIdOrganograma.'Organograma');

    $pgOculOr  = CAM_GA_ADM_PROCESSAMENTO."OCIMontaOrganograma.php?".Sessao::getId();
    $stCampos  = "'".$pgOculOr."&stOrganograma='+this.value+'";
    $stCampos .= "&inNumNiveis=".$this->inCodigoNivel;
    $stCampos .= "&boMostraComboOrganograma=".$this->boMostraComboOrganograma;
    $stCampos .= "&boMostraClassificacao=".$this->boMostraClassificacao;
    $stCampos .= "&boMostraUltimoNivel=".$this->obROrganograma->getMostraUltimoNivel();
    $stCampos .= "&stIdOrganograma=".$this->stIdOrganograma;
    $stCampos .= "&inCodNivelObrigatorio=".$this->getNivelObrigatorio();
    $stCampos .= "&".$this->stIdOrganograma."Organograma='+this.value+'";
    $stCampos .= "&inLarguraRotulo=".$obFormulario->getLarguraRotulo();
    $stCampos .= "&inLarguraComponente=".$this->getStyle();
    $stCampos .= "&hiddenEvalName=".$this->getHiddenEvalName();

    if ($this->stCodClassificacao) {
        $stCampos .= "&stCodClassificacao=".$this->stCodClassificacao;
    }
    $stCampos .= "'";
    $obCmbOrganograma->obEvento->setOnChange("ajaxJavaScript(".$stCampos.", 'montaCombosOrgaos');" );

    $inContador = 1;
    $stSelected = "";
    while (!$rsListaOrganograma->eof()) {
        $stChaveOrganograma  = $rsListaOrganograma->getCampo( "cod_organograma" )."§";
        $stChaveOrganograma .= $rsListaOrganograma->getCampo( "implantacao")."§";
        $stChaveOrganograma .= $rsListaOrganograma->getCampo( "cod_norma")."§";
        $stNomeOrganograma   = $rsListaOrganograma->getCampo( "cod_organograma" );
        $stNomeOrganograma  .= ' - '.$rsListaOrganograma->getCampo( "implantacao" );

        if ($this->stCodClassificacao) {
            if ($rsListaOrganograma->getCampo( "cod_organograma") == $inCodOrganograma) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
        }

        $obCmbOrganograma->addOption($stChaveOrganograma,$stNomeOrganograma,$stSelected);

        $inContador++;
        $rsListaOrganograma->proximo();
    }

    # Novo formulario criado para combos de orgaos que estaram dentro do span
    $obFormularioSpan = new Formulario;
    $obFormularioSpan->setForm(null);
    $obFormularioSpan->setLarguraRotulo($obFormulario->getLarguraRotulo());

    if ($this->stCodClassificacao) {
        $this->montaCombos($obFormularioSpan, false);
    }

    $obFormularioSpan->obJavaScript->montaJavaScript();
    $stEval = $obFormularioSpan->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $stEval = str_replace('"', "&quot;", $stEval);

    # monta o hiddenEval para guardar os scripts de verificação das combos
    $obHdnEvalCombos = new HiddenEval;
    $obHdnEvalCombos->setName('hdnEvalCombos');
    $obHdnEvalCombos->setId  ('hdnEvalCombos');
    $obHdnEvalCombos->setValue($stEval);

    $obFormularioSpan->montaInnerHTML();

    $obSpan = new Span;
    $obSpan->setId($this->stIdOrganograma.'spnCombos');
    $obSpan->setValue($obFormularioSpan->getHTML());

    # Quando for setado um hidden eval do form principal e for passado ao componente ele utiliza esse!
    if ($this->getHiddenEvalName() == '') {
        $obFormulario->addHidden                        ( $obHdnEvalCombos,true );
    } else {
        $stEval = str_replace("&quot;",'"', $stEval);
        $this->setScript($stEval);
    }

    $obFormulario->addComponente                    ($obCmbOrganograma);
    $obFormulario->addSpan                          ($obSpan);
}

/**
    * Monta as informações do organograma apenas para leitura
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function montaLabelOrganograma(&$obFormulario)
{
    $inCodOrganograma = $this->obROrganograma->getCodOrganograma();

    $obErro = $this->obROrganograma->listar($rsListaOrganograma);

    # Caso tenha um organograma setado (edição)
    $this->obROrganograma->setCodOrganograma($inCodOrganograma);

    # Definição do combo dos organogramas
    $obLblOrganograma = new Label;

    $obLblOrganograma->setRotulo   ($this->getRotuloComboOrganograma());

    $obLblOrganograma->setName($this->stIdOrganograma.'Organograma');
    $obLblOrganograma->setId  ($this->stIdOrganograma.'Organograma');

    $inContador = 1;
    while (!$rsListaOrganograma->eof()) {

        $stNomeOrganograma   = $rsListaOrganograma->getCampo( "cod_organograma" );
        $stNomeOrganograma  .= ' - '.$rsListaOrganograma->getCampo( "implantacao" );

        if ($this->stCodClassificacao) {
            if ($rsListaOrganograma->getCampo( "cod_organograma") == $inCodOrganograma) {
                $obLblOrganograma->setValue($stNomeOrganograma);
            }
        }
        $rsListaOrganograma->proximo();
    }

    $obFormulario->addComponente( $obLblOrganograma );

    $obFormularioSpan = new Formulario;
    $obFormularioSpan->setLarguraRotulo($obFormulario->getLarguraRotulo());

    if ($this->stCodClassificacao) {
        $this->montaLabels($obFormularioSpan, false);
    }

    $obFormularioSpan->montaInnerHTML();

    $obSpan = new Span;
    $obSpan->setId($this->stIdOrganograma.'spnCombos');
    $obSpan->setValue($obFormularioSpan->getHTML());
    $obFormulario->addSpan($obSpan);
}

/**
    * Monta as informações dos niveis do organograma para leitura apenas
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function montaLabels(&$obFormulario)
{
    // Se for selecionada a opção Selecione, então não monta nenhum combo
    if ($this->obROrganograma->getCodOrganograma() != -1) {

        // Se não foi setado o cod_organograma, o sistema busca o organograma configurado como ativo
        if (!$this->obROrganograma->getCodOrganograma()) {

            $stCampo = 'cod_organograma';
            $stTabela = 'organograma.organograma';
            $stFiltroOrganograma = ' WHERE ativo = true';

            $inCodOrganograma = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltroOrganograma);

        } else {
            $inCodOrganograma = $this->obROrganograma->getCodOrganograma();
        }

        $this->obROrganograma->setCodOrganograma($inCodOrganograma);

        // Lista todos os níveis do organograma relacionado
        $obErro = $this->obROrganograma->listarNiveis($rsListaNivel);

        // Se não estiver setado o nível limite, pega o número de níveis para aquele organograma
        if ($this->inCodigoNivel != -1) {
            $this->inNumNiveis = $this->inCodigoNivel;
        } else {
            $this->inNumNiveis = $rsListaNivel->getNumLinhas();
        }

        $arLabelsOrganograma = array();
        $boFlagPrimeiroNivel = true;
        $inContNomeCombo = 1;
        $inCodNivel = 1;
        if ($this->stCodClassificacao) {
            $arValorComposto = explode('.', $this->stCodClassificacao);
            $stCodClassificacao = '';
        }

        while (!$rsListaNivel->eof()) {

            if ($this->inCodigoNivel == -1 || $this->inCodigoNivel >= $inCodNivel) {
                $inNumNivel = $rsListaNivel->getCampo( "cod_nivel" );
                $stNomeNivel[$inNumNivel] = $rsListaNivel->getCampo( "descricao" );

                //Definição padrão dos Labels dos órgãos
                $obLblOrganograma = new Label;

                $obLblOrganograma->setRotulo    ( $stNomeNivel[$inNumNivel]    );

                $inContNomeComboAUX = $inContNomeCombo++;
                $obLblOrganograma->setName( $this->stIdOrganograma."_".$inContNomeComboAUX );
                $obLblOrganograma->setId  ( $this->stIdOrganograma."_".$inContNomeComboAUX );

                if ($this->stCodClassificacao) {
                    $this->obROrganograma->obRNivel->setCodNivel($rsListaNivel->getCampo("cod_nivel"));
                    $this->obROrganograma->obRNivel->setMascaraCodigo($stCodClassificacao);
                    $this->obROrganograma->setCodOrganograma($inCodOrganograma);
                    $obErro = $this->obROrganograma->listarOrgaosRelacionadosDescricao($rsListaOrgao);

                    if ($arValorComposto[$inCodNivel - 1]) {
                        $stCodClassificacao .= $arValorComposto[$inCodNivel - 1].'.';
                    }

                    $inContador = 1;
                    while (!$rsListaOrgao->eof()) {

                        $stNomeOrganograma   = $rsListaOrgao->getCampo( "descricao" );

                        if ($rsListaOrgao->getCampo( "valor") == $arValorComposto[$inCodNivel - 1]) {
                            $obLblOrganograma->setValue($stNomeOrganograma);
                            $inUltimoOrgaoSelecionado = $rsListaOrgao->getCampo( "cod_orgao");
                        }
                        $rsListaOrgao->proximo();
                    }
                }

                // Monta a máscara para a função mascaradinamico
                $this->stMascara .= $rsListaNivel->getCampo("mascaracodigo").".";
                if ($obLblOrganograma->getValue() !='') {
                    $arLabelsOrganograma[] = $obLblOrganograma;
                }
            }
            $inCodNivel++;
            $rsListaNivel->proximo();
        }

        $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );

        $obHdnMascara = new Hidden;
        $obHdnMascara->setName ( 'stMascara' );
        $obHdnMascara->setValue ( $this->stMascara );

        if ($this->getHiddenInformacoes()) {
            $obHdnChaveOrganograma = new Hidden;
            $obHdnChaveOrganograma->setName ( 'hdn'.$this->stIdOrganograma );
            $obHdnChaveOrganograma->setId   ( 'hdn'.$this->stIdOrganograma );
            $obHdnChaveOrganograma->setValue   ( $this->stCodClassificacao );

            $obHdnUltimoOrgaoSelecionado = new Hidden;
            $obHdnUltimoOrgaoSelecionado->setName ( 'hdnUltimoOrgaoSelecionado' );
            $obHdnUltimoOrgaoSelecionado->setId   ( 'hdnUltimoOrgaoSelecionado' );
            $obHdnUltimoOrgaoSelecionado->setValue   ( $inUltimoOrgaoSelecionado );
        }

        // Monta o formulário dos níveis do organograma
        if (count($arLabelsOrganograma)) {

            if ($this->boMostraClassificacao) {

                $obLblChaveOrganograma = new Label;

                $obLblChaveOrganograma->setId($this->stIdOrganograma.'Classificacao');
                $obLblChaveOrganograma->setName ($this->stIdOrganograma.'Classificacao');
                $obLblChaveOrganograma->setRotulo ( "Classificação" );

                $obLblChaveOrganograma->setValue(Mascara::geraMascara($this->stMascara, str_replace('.', '', $this->stCodClassificacao)));
            }

            // Guarda o número de níveis para auxiliar o método preenche prox.
            $obHdnNumNiveis = new Hidden;
            $obHdnNumNiveis->setName  ( "inNumNiveis" );
            $obHdnNumNiveis->setValue ( $this->inCodigoNivel );

            $obFormulario->addHidden    ( $obHdnNumNiveis      );
            $obFormulario->addHidden    ( $obHdnMascara        );

            if ($this->getHiddenInformacoes()) {
                $obFormulario->addHidden    ( $obHdnChaveOrganograma );
                $obFormulario->addHidden    ( $obHdnUltimoOrgaoSelecionado );
            }
            if ($this->boMostraClassificacao) {
                $obFormulario->addComponente ( $obLblChaveOrganograma );
            }

            foreach ($arLabelsOrganograma as $obLblOrganograma) {
                $obFormulario->addComponente( $obLblOrganograma );
            }
        }
        $this->arLblOrganograma = $arLabelsOrganograma;
    }
}

/**
    * Monta as combos dos orgãos
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function montaCombos(&$obFormulario, $boRetornaJS = false)
{
    // Se for selecionada a opção Selecione, então não monta nenhum combo
    if ($this->obROrganograma->getCodOrganograma() != -1) {

        // Se não foi setado o cod_organograma, o sistema busca o organograma configurado como ativo
        if (!$this->obROrganograma->getCodOrganograma()) {

            $stCampo = 'cod_organograma';
            $stTabela = 'organograma.organograma';
            $stFiltroOrganograma = ' WHERE ativo = true';

            $inCodOrganograma = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltroOrganograma);

        } else {
            $inCodOrganograma = $this->obROrganograma->getCodOrganograma();
        }

        $this->obROrganograma->setCodOrganograma($inCodOrganograma);

        // Lista todos os níveis do organograma relacionado
        $obErro = $this->obROrganograma->listarNiveis($rsListaNivel);

        // Se não estiver setado o nível limite, pega o número de níveis para aquele organograma
        if ($this->inCodigoNivel != -1) {
            $this->inNumNiveis = $this->inCodigoNivel;
        } else {
            $this->inNumNiveis = $rsListaNivel->getNumLinhas();
        }

        $arCombosOrganograma = array();
        $boFlagPrimeiroNivel = true;
        $inContNomeCombo = 1;
        $inCodNivel = 1;

        if ($this->stCodClassificacao) {
            $arValorComposto = explode('.', $this->stCodClassificacao);
            $stCodClassificacao = '';
        }

        $this->obROrganograma->setCodOrganograma($inCodOrganograma);
        $obErro = $this->obROrganograma->listarOrgaosRelacionadosDescricaoComponente($rsListaOrgao);

        $inContador = 1;
        while (!$rsListaNivel->eof()) {
            if ($this->inCodigoNivel == -1 || $this->inCodigoNivel >= $inCodNivel) {
                $inNumNivel = $rsListaNivel->getCampo( "cod_nivel" );
                $stNomeNivel[$inNumNivel] = $rsListaNivel->getCampo( "descricao" );

                //Definição padrão dos combos dos órgãos
                $obCmbOrganograma = new Select;

                if (( ($this->getNullBarra()) && ($this->getNivelObrigatorio() >= $this->inCodigoNivel)) || ($this->getNivelObrigatorio() == '0')) {
                    $obCmbOrganograma->setNullBarra(true);
                }

                $obCmbOrganograma->setRotulo    ( $stNomeNivel[$inNumNivel]    );
                $obCmbOrganograma->addOption    ( "", "Selecione"   );
                $obCmbOrganograma->setCampoId   ( "[cod_nivel]§[cod_orgao]§[valor]§[cod_organograma]" );
                $obCmbOrganograma->setCampoDesc ( "descricao" );
                $obCmbOrganograma->setStyle     ( $this->getStyle()    );

                if ( ($this->getNivelObrigatorio() >= $inCodNivel) || $this->getNivelObrigatorio() == '0') {
                    $obCmbOrganograma->setNull      ( false  );
                }

                $inContNomeComboAUX = $inContNomeCombo++;
                $obCmbOrganograma->setName( $this->stIdOrganograma."_".$inContNomeComboAUX );
                $obCmbOrganograma->setId  ( $this->stIdOrganograma."_".$inContNomeComboAUX );
                $pgOculOr  = CAM_GA_ADM_PROCESSAMENTO."OCIMontaOrganograma.php?".Sessao::getId();

                $stCampos  = "'".$pgOculOr.'&inPosicao='.$inContNomeCombo;
                $stCampos .= "&".$obCmbOrganograma->getName()."='+this.value+'";
                $stCampos .= "&inNumNiveis=".$this->inNumNiveis;

                if ($this->boMostraClassificacao) {
                    $stCampos .= "&".$this->stIdOrganograma."Classificacao='+jq('#".$this->stIdOrganograma."Classificacao').val()+'";
                }

                $stCampos .= "&hdn".$this->stIdOrganograma."='+jq('#hdn".$this->stIdOrganograma."').val()+'";
                $stCampos .= "&boMostraUltimoNivel=".$this->obROrganograma->getMostraUltimoNivel();
                $stCampos .= "&boMostraClassificacao=".$this->boMostraClassificacao;

                $stCampos .= "&mascaraNivel=".$rsListaNivel->getCampo("mascaracodigo");
                $stCampos .= "&stIdOrganograma=".$this->stIdOrganograma;
                $stCampos .= "'";

                $stCampos = str_replace("'", "&quot;", $stCampos);

                $obCmbOrganograma->obEvento->setOnChange(" ajaxJavaScript(".$stCampos.", &quot;preencheProxCombo&quot;);" );

                if ($this->stCodClassificacao) {
                    while (!$rsListaOrgao->eof()) {
                        $arEstrutural = explode('.', $rsListaOrgao->getCampo('orgao'));

                        if ($rsListaNivel->getCampo('cod_nivel') == $rsListaOrgao->getCampo('cod_nivel')
                        && ($stCodClassificacao == '' || $stCodClassificacao == substr($rsListaOrgao->getCampo('orgao'), 0, $inContador)) ) {
                            $stChaveOrganograma  = $rsListaOrgao->getCampo( "cod_nivel" )."§";
                            $stChaveOrganograma .= $rsListaOrgao->getCampo( "cod_orgao")."§";
                            $stChaveOrganograma .= $rsListaOrgao->getCampo( "valor")."§";
                            $stChaveOrganograma .= $rsListaOrgao->getCampo( "cod_organograma");
                            $stNomeOrganograma   = $rsListaOrgao->getCampo( "descricao" );

                            if ($rsListaOrgao->getCampo( "valor") == $arValorComposto[$inCodNivel - 1]) {
                                $stSelected = "selected";
                                $inUltimoOrgaoSelecionado = $rsListaOrgao->getCampo( "cod_orgao");
                            } else {
                                $stSelected = "";
                            }

                            $obCmbOrganograma->addOption($stChaveOrganograma,$stNomeOrganograma,$stSelected);
                        }

                        $rsListaOrgao->proximo();
                    }

                    $rsListaOrgao->setPrimeiroElemento();

                    if ($arValorComposto[$inCodNivel - 1]) {
                        if ($stCodClassificacao == '')  {
                            $stCodClassificacao .= $arValorComposto[$inCodNivel - 1];
                        } else {
                            $stCodClassificacao .= '.'.$arValorComposto[$inCodNivel - 1];
                        }
                        $inContador = strlen($stCodClassificacao);
                    }

                } elseif ($boFlagPrimeiroNivel) { // Preenche apenas o primeiro nível

                    $boFlagPrimeiroNivel = false;
                    $this->obROrganograma->obRNivel->setCodNivel($rsListaNivel->getCampo("cod_nivel"));
                    $this->obROrganograma->setCodOrganograma($inCodOrganograma);
                    $obErro = $this->obROrganograma->listarOrgaosRelacionadosDescricao($rsListaOrgao);
                    $obCmbOrganograma->preencheCombo($rsListaOrgao);
                }

                // Monta a máscara para a função mascaradinamico
                $this->stMascara .= $rsListaNivel->getCampo("mascaracodigo").".";
                $arCombosOrganograma[] = $obCmbOrganograma;
            }
            $inCodNivel++;
            $rsListaNivel->proximo();
        }

        $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );

        // Grava a mascara na seção para que seja possivel acessa-la no oculto posteriormente e seta-la no componente outra vez
        Sessao::write($this->stIdOrganograma.'mascaraOrganograma',$this->stMascara);

        $arMascara = explode('.',$this->stMascara);
        $numeroNiveisMascara = count($arMascara);
        $tamanhoMascaraCodigo = strlen($arMascara[0]);

        $obHdnMascara = new Hidden;
        $obHdnMascara->setName ( 'stMascara' );
        $obHdnMascara->setValue ( $this->stMascara );

        $obHdnChaveOrganograma = new Hidden;
        $obHdnChaveOrganograma->setName ( 'hdn'.$this->stIdOrganograma );
        $obHdnChaveOrganograma->setId   ( 'hdn'.$this->stIdOrganograma );
        $obHdnChaveOrganograma->setValue   ( $this->stCodClassificacao );

        $obHdnUltimoOrgaoSelecionado = new Hidden;
        $obHdnUltimoOrgaoSelecionado->setName ( 'hdnUltimoOrgaoSelecionado' );
        $obHdnUltimoOrgaoSelecionado->setId   ( 'hdnUltimoOrgaoSelecionado' );
        $obHdnUltimoOrgaoSelecionado->setValue   ( isset($inUltimoOrgaoSelecionado) ? $inUltimoOrgaoSelecionado : "" );

        // Monta o formulário dos níveis do organograma
        if (count($arCombosOrganograma)) {

            if ($this->boMostraClassificacao) {
                // Campo TEXT para a classificação dos órgãos
                $obTxtChaveOrganograma = new TextBox;
                $obTxtChaveOrganograma->setId($this->stIdOrganograma.'Classificacao');
                $obTxtChaveOrganograma->setName ($this->stIdOrganograma.'Classificacao');
                $obTxtChaveOrganograma->setRotulo ( "Classificação" );
                $obTxtChaveOrganograma->setTitle($this->stTitle);
                $obTxtChaveOrganograma->setMaxLength(strlen($this->stMascara));
                $obTxtChaveOrganograma->setSize(strlen($this->stMascara)+5);
                $obTxtChaveOrganograma->setMascara($this->stMascara);
                $obTxtChaveOrganograma->setValue(Mascara::geraMascara($this->stMascara, str_replace('.', '', $this->stCodClassificacao)));

                if ($this->boCadastroOrganograma) {
                    $obTxtChaveOrganograma->setNull(false);
                }

                $pgOculOr  = CAM_GA_ADM_PROCESSAMENTO."OCIMontaOrganograma.php?".Sessao::getId();
                $stCampos  = "'".$pgOculOr;
                $stCampos .= "&".$this->stIdOrganograma.'Classificacao'."='+jq('#".$this->stIdOrganograma.'Classificacao'."').val()+'";

                if ($this->boMostraComboOrganograma) {
                    $stCampos .= "&stOrganograma='+jq('#".$this->stIdOrganograma.'Organograma'."').val()+'";
                    $stCampos .= "&".$this->stIdOrganograma."Organograma='+jq('#".$this->stIdOrganograma.'Organograma'."').val()+'";
                }

                $stCampos .= "&hdn".$this->stIdOrganograma."='+jq('#hdn".$this->stIdOrganograma."').val()+'";

                if ($this->obROrganograma->getMostraUltimoNivel()) {
                    $stCampos .= "&boMostraUltimoNivel=true";
                }

                $stCampos .= "&inNumNiveis=".$this->inNumNiveis;
                $stCampos .= "&stIdOrganograma=".$this->stIdOrganograma;
                $stCampos .= "'";

                $stCampos = str_replace("'", "&quot;", $stCampos);
                $obTxtChaveOrganograma->obEvento->setOnChange("this.value = preencheComZerosPelaMascara(this.value,&quot;".$this->stMascara."&quot;);ajaxJavaScript(".$stCampos.", &quot;preencheCombosOrgaos&quot;);" );
            }

            // Guarda o número de níveis para auxiliar o método preenche prox.
            $obHdnNumNiveis = new Hidden;
            $obHdnNumNiveis->setName  ( "inNumNiveis" );
            $obHdnNumNiveis->setValue ( $this->inCodigoNivel );

            $obFormulario->addHidden    ( $obHdnNumNiveis      );
            $obFormulario->addHidden    ( $obHdnMascara        );
            $obFormulario->addHidden    ( $obHdnChaveOrganograma );
            $obFormulario->addHidden    ( $obHdnUltimoOrgaoSelecionado );

            if ($this->boMostraClassificacao) {
                $obFormulario->addComponente ( $obTxtChaveOrganograma );
            }

            foreach ($arCombosOrganograma as $obCmbOrganograma) {
                $obFormulario->addComponente( $obCmbOrganograma );
            }
        }
        $this->arCmbOrganograma = $arCombosOrganograma;
    }
}

/**
    * Monta os combos do organograma conforme o nível setado
    * @access Public
    * @param Integer $inPosCombo Posição do combo no formulário
    * @param Integer $inNumCombos Número de combos no formulário
*/
function preencheProxCombo($inPosCombo, $inNumCombos)
{
    $obErro = $this->obROrganograma->listarNiveis($rsListaNivel);

    if ($this->obROrganograma->getMostraUltimoNivel()) {
        $inNumCombos = 1;
    }

    // Limpa todas as combos seguintes a combo com valor selecionado
    for ($inCont = $inPosCombo; $inCont <= $inNumCombos; $inCont++) {
        if (($inCont != $inPosCombo)&&($inCont != 1)) {
            $rsListaNivel->setCorrente($inCont);
            $stNomeCombo = $this->stIdOrganograma."_".$inCont;
            $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
            $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione','', 'selected');\n";
        }
    }

    $rsListaOrgao = new RecordSet;

    $inValorReduzido = explode('.',$this->stValorReduzido);

    $this->stValorComposto .= $this->stValorReduzido;

    if ($inValorReduzido[$inPosCombo-1] != "") {
        $this->obROrganograma->obRNivel->setCodNivel($inPosCombo+1);
        $this->obROrganograma->obRNivel->setMascaraCodigo($this->stValorComposto);
        $obErro = $this->obROrganograma->listarOrgaosRelacionadosDescricao($rsListaOrgao);
    }

    $inContador = 1;

    // Monta as próximas combos de acordo com o órgão selecionado
    if ($inPosCombo+1 <= $inNumCombos) {
        while (!$rsListaOrgao->eof()) {
            $stChaveOrganograma  = $rsListaOrgao->getCampo( "cod_nivel" )."§";
            $stChaveOrganograma .= $rsListaOrgao->getCampo( "cod_orgao")."§";
            $stChaveOrganograma .= $rsListaOrgao->getCampo( "valor")."§";
            $stChaveOrganograma .= $rsListaOrgao->getCampo( "cod_organograma");
            $stNomeOrganograma   = $rsListaOrgao->getCampo( "descricao" );

            $inProxPosCombo = $inPosCombo+1;

            $js .= "f.".$this->stIdOrganograma."_".$inProxPosCombo.".options[$inContador] = ";
            $js .= "new Option('".$stNomeOrganograma."','".$stChaveOrganograma."',''); \n";
            $inContador++;
            $rsListaOrgao->proximo();
        }
    }

    if ($this->boMostraClassificacao) {
        $js.="f.".$this->stIdOrganograma.'Classificacao'.".value = preencheComZerosPelaMascara('".$this->stValorComposto."','".$this->stMascara."'); ";
    }

    $js .= "f.hdn".$this->stIdOrganograma.".value = '".$this->stValorComposto."';\n";

    $js .= "f.hdnUltimoOrgaoSelecionado.value = '';\n";

    if ($this->getUltimoOrgaoSelecionado()) {
        $js .= "f.hdnUltimoOrgaoSelecionado.value = '".$this->getUltimoOrgaoSelecionado()."';\n";
    } else {

        $js .= "    var comboOrgaoValor = new String;
                    var valorPreenchido = false;
                    var indice = 0;
                    for (indice = ".$inNumCombos.";indice > 0; indice--) {
                        comboOrgaoValor = jQuery('#inCodOrganograma_'+indice).val();
                        if ( (comboOrgaoValor != '') && (valorPreenchido != true)) {
                            arValoresCombo = comboOrgaoValor.split('§');
                            f.hdnUltimoOrgaoSelecionado.value = arValoresCombo[1];
                            valorPreenchido = true;
                        }
                    }
               ";
    }

    if (!$this->getRetornaJS()) {
        echo $js;
    } else {
        return $js;
    }
}

/**
    * Preenche os combos a partir da chave do organograma
    * @access Public
*/
function preencheCombosOrgaos($inNumCombos)
{
    $js .= "f.hdnUltimoOrgaoSelecionado.value = '';\n";
    // Se não foi setado o cod_organograma, o sistema busca o organograma configurado como ativo
    if (!$this->obROrganograma->getCodOrganograma()) {
        $stCampo = 'cod_organograma';
        $stTabela = 'organograma.organograma';
        $stFiltroOrganograma = ' WHERE ativo = true';

        $inCodOrganograma = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltroOrganograma);
    } else {
        $inCodOrganograma = $this->obROrganograma->getCodOrganograma();
    }

    $this->obROrganograma->setCodOrganograma($inCodOrganograma);

    $obErro = $this->obROrganograma->listarNiveis($rsListaNivel);

    // Pega a classificação digitada pelo usuário
    $arValorReduzido = explode( ".", $this->stValorReduzido );

    $inCont = 1; // Contador dos combos dos níveis do organograma

    if ($this->obROrganograma->getMostraUltimoNivel()) {
        $inCount = $rsListaNivel->getCampo('cod_nivel');
    } else {
        $inCount = 1;
    }

    $stChave = '';
    $stValorReduzido = "";

    if (($arValorReduzido[0] =="") || (str_replace('.','',$arValorReduzido[0]) == 0)) {
        while (!$rsListaNivel->eof() and key( $arValorReduzido ) < (count( $arValorReduzido )+1)) {

            $stNomeCombo = $this->stIdOrganograma."_".$inCont;

            if ($inCont > 1) {
                // Limpa as combos seguintes
                $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
                $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione','', 'selected');\n";

                echo $js;
            } else {
                $js .= "f.".$stNomeCombo.".options[0].selected = 'selected';";
            }
            $inCont++;
            $rsListaNivel->proximo();
        }

    } else {
        while (!$rsListaNivel->eof() && $inCont <= (count( $arValorReduzido )+1)) {
            if ($inCont <= $this->inNumNiveis) {
                $stChave .= $arValorReduzido[$inCount-1];

                if ($arValorReduzido[$inCount-2]) {
                    $stMascaraFiltro .= $arValorReduzido[$inCount-2].'.';
                }

                $stNomeCombo = $this->stIdOrganograma."_".$inCont++;

                // Limpa as combos seguintes
                $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
                $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione','', 'selected');\n";

                echo $js;

                $this->obROrganograma->obRNivel->setCodNivel($inCount);
                $this->obROrganograma->obRNivel->setMascaraCodigo($stMascaraFiltro);
                $obErro = $this->obROrganograma->listarOrgaosRelacionadosDescricao($rsListaOrgao);
                $stChave .= '.';

                $inContador = 1;
                // Monta as combos de acordo com o órgão selecionado anteriormente
                 while (!$rsListaOrgao->eof()) {
                    $stChaveOrganograma  = $rsListaOrgao->getCampo( "cod_nivel" )."§";
                    $stChaveOrganograma .= $rsListaOrgao->getCampo( "cod_orgao")."§";
                    $stChaveOrganograma .= $rsListaOrgao->getCampo( "valor")."§";
                    $stChaveOrganograma .= $rsListaOrgao->getCampo( "cod_organograma");
                    $stNomeOrganograma   = $rsListaOrgao->getCampo( "descricao" );
                    if ($rsListaOrgao->getCampo( "valor") == $arValorReduzido[$inCont-2]) {
                        $inSelected = $inContador;
                        $boFlag = true;
                        $js .= "f.hdnUltimoOrgaoSelecionado.value = '".$rsListaOrgao->getCampo( "cod_orgao")."';\n";
                    }
                    $js .= "f.".$stNomeCombo.".options[$inContador] = ";
                    $js .= "new Option('".$stNomeOrganograma."','".$stChaveOrganograma."'); \n";

                    if (isset($inSelected) && ($boFlag == true)) {
                        $js .= "f.".$stNomeCombo.".options[".$inSelected."].selected  = 'selected'; \n";
                        $boFlag = false;
                    }

                    $inContador++;
                    $rsListaOrgao->proximo();
                }
                $inCount++;
            }
            $rsListaNivel->proximo();
        }
    }

    $cont = 0;
    $stChaveOrganogramaTMP = '';

    while ($cont < (strlen($this->getMascara()) - strlen($_REQUEST[$this->stIdOrganograma.'Classificacao']))) {
        $stChaveOrganogramaTMP .= '0';
        $cont++;
    }

    $stChaveOrganogramaTMP .= $_REQUEST[$this->stIdOrganograma.'Classificacao'] ;

    if ($this->boMostraClassificacao) {
        $js .= "f.".$this->stIdOrganograma.'Classificacao'.".value='".$stChaveOrganogramaTMP."';\n";
    }

    $js .= "f.hdn".$this->stIdOrganograma.".value='".$stChaveOrganogramaTMP ."';\n";
/*
    $js .= "    var comboOrgaoValor = new String;
                    var valorPreenchido = false;
                    var indice = 0;
                    for (indice = ".$inNumCombos.";indice > 0; indice--) {
                        comboOrgaoValor = jQuery('#inCodOrganograma_'+indice).val();
                        if ( (comboOrgaoValor != '') && (valorPreenchido != true)) {
                            arValoresCombo = comboOrgaoValor.split('§');
                            f.hdnUltimoOrgaoSelecionado.value = arValoresCombo[1];
                            valorPreenchido = true;
                        }
                    }
               ";
*/
    if (!$this->getRetornaJs()) {
        echo $js;
    } else {
        return $js;
    }
}

}
?>
