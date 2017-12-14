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
    * Arquivo de popup de busca de CGM
    * Data de Criação: 29/08/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage

    $Revision: 30824 $
    $Name$
    $Author: cleisson $
    $Date: 2006-10-13 13:06:30 -0300 (Sex, 13 Out 2006) $

    * Casos de uso: uc-02.01.00, uc-02.03.29
*/

/*
$Log$
Revision 1.3  2006/10/13 16:05:54  cleisson
Ajuste

Revision 1.2  2006/09/25 13:56:13  cleisson
Bug #7034#

Revision 1.1  2006/09/01 15:07:05  jose.eduardo
Inclusão de componente
*/

include_once( CLA_LABEL );

class ILabelEntidade extends Label
{
    public $obForm;
    public $inCodEntidade;
    public $stExercicio;
    public $obHdnCodEntidade;
    public $boMostraCodigo = false;

    public function ILabelEntidade(&$obForm)
    {
        parent::Label();

        $this->obForm = $obForm;
        $this->obHdnCodEntidade = new Hidden;
        $this->obHdnCodEntidade->setName ( 'inCodEntidade' );
        $this->obHdnCodEntidade->setId   ( 'inCodEntidade' );
        $this->setRotulo ("Entidade" );
        $this->setName   ("stEntidade" );
        $this->setId     ("stEntidade" );
        $this->stExercicio = Sessao::getExercicio();
    }

    public function setCodEntidade($value)
    {
        $this->inCodEntidade = $value;
    }

    public function getCodEntidade()
    {
        return $this->inCodEntidade;
    }

    public function setExercicio($value)
    {
        $this->stExercicio = $value;
    }

    public function getExercicio()
    {
        return $this->stExercicio;
    }

    public function setMostraCodigo($valor)
    {
        $this->boMostraCodigo = $valor;
    }

    public function montaHTML()
    {
        include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"    );
        $obROrcamentoEntidade = new ROrcamentoEntidade;

        if ( $this->getCodEntidade() ) {
            $obROrcamentoEntidade->setCodigoEntidade($this->getCodEntidade());
        }
        if ( $this->getExercicio() ) {
            $obROrcamentoEntidade->setExercicio($this->getExercicio());
        }

        $obROrcamentoEntidade->listar($rsRecordSet);

        if ($this->boMostraCodigo) {
            $this->setValue ( $rsRecordSet->getCampo('cod_entidade').' - '.$rsRecordSet->getCampo('nom_cgm') );
        } else {
            $this->setValue ( $rsRecordSet->getCampo('nom_cgm') );
        }
        $this->obHdnCodEntidade->setValue( $rsRecordSet->getCampo( 'cod_entidade' ) );

        parent::montaHTML();
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->montaHTML();

        $obFormulario->addHidden    ($this->obHdnCodEntidade);
        $obFormulario->addComponente($this);
    }
}
?>
