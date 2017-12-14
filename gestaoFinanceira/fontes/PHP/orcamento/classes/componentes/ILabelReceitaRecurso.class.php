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

    $Revision: 32894 $
    $Name$
    $Author: domluc $
    $Date: 2006-10-26 13:30:31 -0300 (Qui, 26 Out 2006) $

    * Casos de uso: uc-02.01.00 , uc-02.04.04
*/

include_once( CLA_LABEL );

class ILabelReceitaRecurso extends Label
{
    public $obForm;
    public $inCodReceita;
    public $stExercicio;
    public $obHdnCodReceita;
    public $boMostraCodigo = false;
    public $boMostraMascaraClass = false;

    public function ILabelReceitaRecurso($obForm)
    {
        parent::Label();

        $this->obForm = $obForm;
        $this->obHdnCodReceita = new Hidden;
        $this->setRotulo ("Receita");
        $this->setName   ("stReceita" );
        $this->setId     ("stReceita" );
        $this->stExercicio = Sessao::getExercicio();
    }

    public function setCodReceita($value)
    {
        $this->inCodReceita = $value;
    }

    public function getCodReceita()
    {
        return $this->inCodReceita;
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
    public function setMostraMascaraClass($valor)
    {
        $this->boMostraMascaraClass = $valor;
    }

    public function montaHTML()
    {
        include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"    );
        $obROrcamentoReceita = new ROrcamentoReceita;

        if ( $this->getCodReceita() ) {
            $obROrcamentoReceita->setCodReceita($this->getCodReceita());
        }
        if ( $this->getExercicio() ) {
            $obROrcamentoReceita->setExercicio($this->getExercicio());
        }

        $obROrcamentoReceita->listar($rsRecordSet);

        if ($this->boMostraCodigo) {
            $this->setValue ( $rsRecordSet->getCampo('cod_receita').' - '.$rsRecordSet->getCampo('descricao') );
        } elseif ($this->boMostraMascaraClass) {
            $this->setValue ( $rsRecordSet->getCampo('mascara_classificacao').' - '.$rsRecordSet->getCampo('descricao') );
        } else {
            $this->setValue ( $rsRecordSet->getCampo('descricao') );
        }
        $this->obHdnCodReceita->setValue( $rsRecordSet->getCampo( 'cod_receita' ) );
        $this->obHdnCodReceita->setName ( 'inCodReceita' );

        parent::montaHTML();
    }
}
?>
