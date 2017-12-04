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
    * Arquivo do componente para norma
    * Data de Criação: 13/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-01.04.02

*/

include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );

class IPopUpNorma extends Objeto
{
    public $obInnerNorma;
    public $obTNorma;
    public $inCodNorma;
    public $boExibeDataNorma;
    public $obLblDataNorma;
    public $boExibeDataPublicacao;
    public $obLblDataPublicacao;

    public function setExibeDataNorma($boValor)
    {
        $this->boExibeDataNorma = $boValor;
    }

    public function setExibeDataPublicacao($boValor)
    {
        $this->boExibeDataPublicacao = $boValor;
    }

    public function getExibeDataNorma()
    {
        return $this->boExibeDataNorma;
    }

    public function getExibeDataPublicacao()
    {
        return $this->boExibeDataPublicacao;
    }

    public function IPopUpNorma()
    {
        $this->obTNorma = new TNorma;

        $this->setExibeDataNorma( false );
        $this->setExibeDataPublicacao( false );

        $this->obInnerNorma = new BuscaInner;
        $this->obInnerNorma->setTitle            ( "Busca norma." );
        $this->obInnerNorma->setNull             ( false );
        $this->obInnerNorma->setRotulo           ( "Norma" );
        $this->obInnerNorma->setId               ( "stNorma" );
        $this->obInnerNorma->obCampoCod->setName ( "inCodNorma" );

        $this->obLblDataNorma = new Label;
        $this->obLblDataNorma->setRotulo( "Data da Norma" );
        $this->obLblDataNorma->setName  ( "stDataNorma" );
        $this->obLblDataNorma->setId    ( "stDataNorma" );
        $this->obLblDataNorma->setValue ( "&nbsp;" );

        $this->obLblDataPublicacao = new Label;
        $this->obLblDataPublicacao->setRotulo( "Data da Publicação" );
        $this->obLblDataPublicacao->setName  ( "stDataPublicacao" );
        $this->obLblDataPublicacao->setId    ( "stDataPublicacao" );
        $this->obLblDataPublicacao->setValue ( "&nbsp;" );
    }

    public function setCodNorma($inValor)
    {
        $this->inCodNorma = $inValor;
    }

    public function geraFormulario(&$obFormulario)
    {

        if ($this->inCodNorma) {
            $stFiltro = " WHERE cod_norma = ".$this->inCodNorma;

            $this->obTNorma->recuperaNormas( $rsNorma, $stFiltro );
            if ( !$rsNorma->eof() ) {
                $this->obInnerNorma->setValue( $rsNorma->getCampo( "nom_norma" ) );
                $this->obInnerNorma->obCampoCod->setValue( $this->inCodNorma );
                if( $this->getExibeDataNorma() )
                    $this->obLblDataNorma->setValue ( $rsNorma->getCampo("dt_assinatura_formatado") );
                if( $this->getExibeDataPublicacao() )
                    $this->obLblDataPublicacao->setValue ( $rsNorma->getCampo("dt_publicacao") );
            }
        }

        //Nova inclusão do componente, para conversão do componente dinamico
        $stCodNorma = $this->obInnerNorma->obCampoCod->getName();
        $stLNorma = $this->obInnerNorma->getId();
        //inclusão feita por Jânio Eduardo

        $pgOcul = CAM_GA_NORMAS_INSTANCIAS."norma/OCManterNorma.php?".Sessao::getId();
        $stOnChange = "ajaxJavaScript('".$pgOcul."&".$stCodNorma."='+this.value+'&boExibeDataNorma=".$this->getExibeDataNorma()."&boExibeDataPublicacao=".$this->getExibeDataPublicacao()."&stL=".$stCodNorma."&st=".$stLNorma."','PreencheNorma');";
        if(!$this->obInnerNorma->obCampoCod->obEvento->getOnChange())
            $this->obInnerNorma->obCampoCod->obEvento->setOnChange( $stOnChange );

        $this->obInnerNorma->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."../../normas/popups/normas/FLNorma.php','frm','".$stCodNorma."','".$stLNorma."','todos','".Sessao::getId()."&boExibeDataNorma=".$this->getExibeDataNorma()."&boExibeDataPublicacao=".$this->getExibeDataPublicacao()."','800','550');" );

        $obFormulario->addComponente( $this->obInnerNorma );
        if( $this->getExibeDataNorma() )
           $obFormulario->addComponente( $this->obLblDataNorma );
        if( $this->getExibeDataPublicacao() )
            $obFormulario->addComponente( $this->obLblDataPublicacao );

    }

}
?>
