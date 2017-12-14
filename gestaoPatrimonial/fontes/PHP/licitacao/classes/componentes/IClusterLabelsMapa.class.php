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
    * Arquivo do componente IClusterLabelMapa
    * Data de Criação: 27/10/2006

    * @author Desenvolvedor: Lucas Teixeira Stephanou

    $Revision: 21348 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-03-27 10:42:23 -0300 (Ter, 27 Mar 2007) $

    * Casos de uso: uc-03.05.25
*/

include_once ( CLA_OBJETO );

class  IClusterLabelMapa extends Objeto
{
    public $obLblNumEdital;
    public $obLblNumLicitacao;
    public $obLblObjeto;
    public $obLblModalidade;
    public $stExercicioMapa;
    public $inCodMapa;
    public $boDispensa;

    public function setExercicio($valor) { $this->stExercicioMapa = $valor; }
    public function setCodMapa($valor) { $this->inCodMapa = $valor; }

    public function getExercicio() { return $this->stExercicioMapa; }
    public function getCodMapa() { return $this->inCodMapa; }

    public function setFiltro($stFiltro) { $this->stFiltro = $stFiltro; }
    public function getFiltro() { return $this->stFiltro; }

    public function IClusterLabelMapa(&$obForm  , $inCodMapa , $stExercicioMapa)
    {
        $this->setCodMapa ( $inCodMapa );
        $this->setExercicio ( $stExercicioMapa );

        parent::Objeto();

        $this->boDispensa = FALSE;

        $this->obLblNumEdital = new Label;
        $this->obLblNumEdital->setId ( 'stNumEdital' );
        $this->obLblNumEdital->setName ( 'stNumEdital');
        $this->obLblNumEdital->setRotulo ( 'Número do Edital' );
        $this->obLblNumEdital->setTitle ( 'Número do Edital' );
        $this->obLblNumEdital->setValue ( '&nbsp;');

        $this->obLblNumLicitacao = new Label;
        $this->obLblNumLicitacao->setId ( 'stNumLicitacao' );
        $this->obLblNumLicitacao->setName ( 'stNumLicitacao' );
        $this->obLblNumLicitacao->setRotulo ( 'Número da Licitação' );
        $this->obLblNumLicitacao->setTitle ( 'Número da Licitação');
        $this->obLblNumLicitacao->setValue ( '&nbsp;');

        $this->obLblObjeto = new Label;
        $this->obLblObjeto->setId ( 'stObjeto' );
        $this->obLblObjeto->setName ( 'stObjeto' );
        $this->obLblObjeto->setRotulo ( 'Objeto' );
        $this->obLblObjeto->setTitle ( 'Objeto' );
        $this->obLblObjeto->setValue ( '&nbsp;' );

        $this->obLblModalidade = new Label;
        $this->obLblModalidade->setId ( 'stModalidade' );
        $this->obLblModalidade->setName ( 'stModalidade' );
        $this->obLblModalidade->setRotulo ( 'Modalidade' );
        $this->obLblModalidade->setTitle ( 'Modalidade' );
        $this->obLblModalidade->setValue ( '&nbsp;' );

        $this->obLblMapaCompras = new Label;
        $this->obLblMapaCompras->setId ( 'stMapaCompras' );
        $this->obLblMapaCompras->setName ( 'stMapaCompras' );
        $this->obLblMapaCompras->setRotulo ( 'Mapa de Compras' );
        $this->obLblMapaCompras->setTitle ( 'Mapa de Compras' );
        $this->obLblMapaCompras->setValue ( '&nbsp;' );

        $this->obLblEntidade = new Label;
        $this->obLblEntidade->setId ( 'stEntidade' );
        $this->obLblEntidade->setName ( 'stEntidade' );
        $this->obLblEntidade->setRotulo ( 'Entidade' );
        $this->obLblEntidade->setTitle ( 'Entidade' );
        $this->obLblEntidade->setValue ( '&nbsp;' );

    }

    public function geraFormulario(&$obFormulario)
    {
        include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapa.class.php' );
        $obTComprasMapa = new TComprasMapa();

        $obTComprasMapa->setDado( 'exercicio', $this->getExercicio() );
        $obTComprasMapa->setDado( 'cod_mapa', $this->getCodMapa() );

        $stFiltro = $this->getFiltro();

        $obTComprasMapa->recuperaMapaLicitacaoProposta( $rsRecordSet, $stFiltro );

        if ( $rsRecordSet->getNumLinhas() > 0 ) {

            $this->obLblEntidade->setValue( $rsRecordSet->getCampo( 'cod_entidade' ).' - '.$rsRecordSet->getCampo( 'entidade' ) );
            if ( $rsRecordSet->getCampo( 'num_edital' ) )
                $this->obLblNumEdital->setValue     ( $rsRecordSet->getCampo( 'num_edital' ) . "/" . $rsRecordSet->getCampo( 'exercicio_edital' ) );
            if ( $rsRecordSet->getCampo( 'cod_licitacao' ) )
                $this->obLblNumLicitacao->setValue  ( $rsRecordSet->getCampo( 'cod_licitacao' ) . "/" . $rsRecordSet->getCampo( 'exercicio_licitacao' ) );
            if ( $rsRecordSet->getCampo( 'cod_modalidade' ) )
                $this->obLblModalidade->setValue    ( $rsRecordSet->getCampo( 'cod_modalidade' ).'-'.$rsRecordSet->getCampo( 'descricao' ) );
            $this->obLblObjeto->setValue        ( stripslashes(nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsRecordSet->getCampo( 'descricao_objeto' ) )))) );
            $this->obLblMapaCompras->setValue( $rsRecordSet->getCampo( 'cod_mapa' ).'/'.$rsRecordSet->getCampo( 'exercicio' ) );
            $obFormulario->addComponente( $this->obLblEntidade );
            if (!$this->boDispensa) {
                $obFormulario->addComponente( $this->obLblNumLicitacao  );
                $obFormulario->addComponente( $this->obLblNumEdital  );
            }
            $obFormulario->addComponente( $this->obLblMapaCompras );
            $obFormulario->addComponente( $this->obLblObjeto  );
            $obFormulario->addComponente( $this->obLblModalidade );
        }
    }
}
?>
