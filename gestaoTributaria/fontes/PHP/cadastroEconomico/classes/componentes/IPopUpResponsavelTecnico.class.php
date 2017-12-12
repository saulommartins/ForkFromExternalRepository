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
    * Arquivo que monta inner de busca empresas
    * Data de Criação: 18/03/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-05.02.04

*/

/*
$Log$

*/

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMResponsavelTecnico.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class IPopUpResponsavelTecnico extends Objeto
{
    public $obInnerResponsavelTecnico;
    public $inNumCGMResponsavelTecnico;

    public function IPopUpResponsavelTecnico()
    {
        //-------------
        Sessao::remove( "arProfissoes" );
        $this->obInnerResponsavelTecnico = new BuscaInner;
        $this->obInnerResponsavelTecnico->setRotulo               ( "Responsável Técnico" );
        $this->obInnerResponsavelTecnico->setTitle                ( "Informe o CGM do responsável técnico." );
        $this->obInnerResponsavelTecnico->setId                   ( "stRespTecnico" );
        $this->obInnerResponsavelTecnico->obCampoCod->setNull     ( NULL );
        $this->obInnerResponsavelTecnico->obCampoCod->setName     ( "inRespTecnico" );
    }

    public function setNumCGMResponvalTecnico($inValor)
    {
        $this->inNumCGMResponsavelTecnico = $inValor;
    }

    public function setProfissoes($arProfissoes)
    {
        //-------------
        Sessao::write( "arProfissoes", $arProfissoes );
    }

    public function getNumCGMResponvalTecnico()
    {
        return $this->inNumCGMResponsavelTecnico;
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->obInnerResponsavelTecnico->setFuncaoBusca ( "abrePopUp('".CAM_GT_CEM_POPUPS."responsaveltecnico/FLProcurarResponsavel.php','frm','".$this->obInnerResponsavelTecnico->obCampoCod->getName()."','".$this->obInnerResponsavelTecnico->getId()."','todos','".Sessao::getId()."','800','550');" );

        if ($this->inNumCGMResponsavelTecnico) {
                $stFiltro = " WHERE numcgm = ".$this->inNumCGMResponsavelTecnico;
                $obTCEMResponsavelTecnico = new TCEMResponsavelTecnico;
                $obTCEMResponsavelTecnico->recuperaRelacionamento( $rsListaResponsavelTecnico, $stFiltro );
                if ( !$rsListaResponsavelTecnico->eof() ) {
                    $this->obInnerResponsavelTecnico->setValue( $rsListaResponsavelTecnico->getCampo("nom_cgm") );
                    $this->obInnerResponsavelTecnico->obCampoCod->setValue( $this->inInscricaoEconomica );
                }
        }

        $stFuncaoChange = 'PreencheResponsavelTecnico';
        $pgOcul = "'".CAM_GT_CEM_INSTANCIAS."resptecnico/OCManterResponsavel.php?".Sessao::getId()."&".$this->obInnerResponsavelTecnico->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerResponsavelTecnico->obCampoCod->getName()."&stId=".$this->obInnerResponsavelTecnico->getId()."'";
        $stOnChange = "ajaxJavaScriptSincrono( ".$pgOcul.", '".$stFuncaoChange."' );";

           $this->obInnerResponsavelTecnico->obCampoCod->obEvento->setOnChange( $stOnChange );

           $obFormulario->addComponente( $this->obInnerResponsavelTecnico );
    }

}
?>
