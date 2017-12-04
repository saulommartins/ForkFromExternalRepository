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
    * Arquivo do componente para busca Modalidade
    * Data de Criação: 25/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpModalidade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.07

*/

include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );

class IPopUpModalidade extends Objeto
{
    public $obInnerModalidade;
    public $obTDATModalidade;
    public $inCodModalidade;
    public $inCodTipoModalidade; //filtrar para cobranca, inscricao
    public $boNull;
    public $stFuncao;

    public function setTipoModalidade($valor) { $this->inCodTipoModalidade = $valor; }
    public function setNull($valor) { $this->boNull = $valor; }
    public function setFuncao($valor) { $this->stFuncao = $valor; }
    public function getNull() { return $this->boNull; }
    public function getTipoModalidade() { return $this->inCodTipoModalidade; }

    public function IPopUpModalidade()
    {

        $this->inCodTipoModalidade = 0;
        $this->obTDATModalidade = new TDATModalidade;
        $this->obInnerModalidade = new BuscaInner;
        $this->obInnerModalidade->setNull             ( $this->getNull() );
        $this->obInnerModalidade->setTitle            ( "Busca Modalidade." );
        $this->obInnerModalidade->setRotulo           ( "Modalidade" );
        $this->obInnerModalidade->setId               ( "stDescricaoModalidade"  );
        $this->obInnerModalidade->obCampoCod->setName ( "inCodModalidade" );
        $this->obInnerModalidade->obCampoCod->setInteiro ( true );

       $this->stFuncao = '';
   }

   public function setCodModalidade($inValor)
   {
       $this->inCodModalidade = $inValor;
   }

   public function geraFormulario(&$obFormulario)
   {
        if ($this->stFuncao == '') {
            $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."modalidade/OCManterModalidade.php?".Sessao::getId()."&".$this->obInnerModalidade->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerModalidade->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerModalidade->getId()."&tipoModalidade=".$this->inCodTipoModalidade."'";
            $stFuncao = "ajaxJavaScript(".$pgOcul.",'buscaModalidade' );";
        } else {
            $stFuncao = $this->stFuncao;
        }

        $this->obInnerModalidade->obCampoCod->obEvento->setOnChange ( $stFuncao );

        $this->obInnerModalidade->setFuncaoBusca      (  "abrePopUp('" . CAM_GT_DAT_POPUPS . "modalidade/FLProcurarModalidade.php','frm', '". $this->obInnerModalidade->obCampoCod->stName ."','". $this->obInnerModalidade->stId . "','".$this->inCodTipoModalidade."','". Sessao::getId() ."','800','550');" );

       if ($this->inCodModalidade) {
            $stFiltro = " WHERE ativa = 't' AND cod_modalidade = ".$this->inCodModalidade;
            $this->obTDATModalidade->recuperaTodos( $rsModalidade, $stFiltro );
            if ( !$rsModalidade->Eof() ) {
                $this->obInnerModalidade->setValue( $rsModalidade->getCampo("descricao") );
                $this->obInnerModalidade->obCampoCod->setValue( $this->inCodModalidade );
            }
       }

       $obFormulario->addComponente( $this->obInnerModalidade );
   }

}
?>
