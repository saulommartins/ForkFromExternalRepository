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
* Data de Criação: 18/08/2006

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GA_PROT_COMPONENTES."JSIChkDocumentoProcesso.js";

class IChkDocumentoProcesso extends Componente
{
    public $inCodigoClassificacao;
    public $inCodigoAssunto;
    public $rsDocumentos;
    public $inCodProcesso;
    public $stAnoProcesso;

    public function IChkDocumentoProcesso()
    {
        parent::Componente();
        $this->setRotulo ("Documentos");
    }

    public function setCodigoClassificacao($valor) { $this->inCodigoClassificacao = $valor; }
    public function setCodigoAssunto($valor)       { $this->inCodigoAssunto = $valor; }
    public function setDocumentos($valor)          { $this->rsDocumentos = $valor; }
    public function setCodProcesso($valor)         { $this->inCodProcesso = $valor; }
    public function setAnoProcesso($valor)         { $this->stAnoProcesso = $valor; }

    public function getCodigoClassificacao() { return $this->inCodigoClassificacao; }
    public function getCodigoAssunto()       { return $this->inCodigoAssunto; }
    public function getCodProcesso()         { return $this->inCodProcesso; }
    public function getAnoProcesso()         { return $this->stAnoProcesso; }

    public function montaChkDocumentos()
    {
        include_once( CAM_GA_PROT_MAPEAMENTO."TPRODocumentoAssunto.class.php" );
        $obTPRODocumentoAssunto =  new TPRODocumentoAssunto();
        $stFiltro .= " AND SW_DOCUMENTO_ASSUNTO.cod_classificacao = ".$this->getCodigoClassificacao();
        $stFiltro .= " AND SW_DOCUMENTO_ASSUNTO.cod_assunto = ".$this->getCodigoAssunto();
        $obTPRODocumentoAssunto->recuperaRelacionamento($rsDocumento, $stFiltro," SW_DOCUMENTO.nom_documento" );
        $arDocumento = array();
        while (!$rsDocumento->eof()) {
            $obChkDocumento = new CheckBox();
            $obChkDocumento->setRotulo ("Documentos");
            $obChkDocumento->setName   ("arCodigoDocumento[]");
            $obChkDocumento->setLabel  ($rsDocumento->getCampo('nom_documento'));
            $obChkDocumento->setValue  ($rsDocumento->getCampo('cod_documento'));
            $obChkDocumento->setChecked(true);

            $obBtnDocumento = new Button();
            $obBtnDocumento->setRotulo ("Documentos");
            $obBtnDocumento->setName('btDocumento'.$rsDocumento->getCampo('cod_documento') );
            $obBtnDocumento->setValue('Cópia Digital');
            
            $stEventoOnClick = "copiaDigital(".$rsDocumento->getCampo('cod_documento').", ".$this->getCodProcesso().", ".$this->getAnoProcesso().");";

            $obBtnDocumento->obEvento->setOnClick($stEventoOnClick);

            $this->roFormulario->obJavaScript->addComponente($obBtnDocumento);
            $this->roFormulario->obJavaScript->addComponente($obChkDocumento);

            $arDocumento[] = array( $obChkDocumento,$obBtnDocumento );
            unset($obChkDocumento);
            unset($obBtnDocumento);
            $rsDocumento->proximo();
        }

        return $arDocumento;
    }

    public function montaHTML()
    {
        $arDocumento = $this->montaChkDocumentos();
        $stHTML = "<table width='100%' >";
        foreach ($arDocumento as $arComponentes) {
            $arComponentes[0]->montaHTML();
            $arComponentes[1]->montaHTML();
            $stHTML .= "<tr><td class=field>".$arComponentes[0]->getHTML()."</td>";
            $stHTML .= "<td class=field>".$arComponentes[1]->getHTML()."</td></tr>";
        }
        $stHTML .= "</table>";
        $this->setHtml($stHTML);
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->roFormulario = $obFormulario;
        $obFormulario->addComponente($this);
    }
}

?>
