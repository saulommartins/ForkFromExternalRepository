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
  * Arquivo de select de Resurso
  * Data de Criação: 15/05/2008

  * @author Analista: Gelson W. Golçalves
  * @author Desenvolvedor: Henrique Girardi dos Santos

  * @package URBEM
  * @subpackage

  * $Id: ISelectMultiploRecurso.class.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso: --
*/

require_once CLA_SELECT_MULTIPLO;
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";

class  ISelectMultiploRecurso extends SelectMultiplo
{

    public $stFiltro;
    public $stExercicio;
    public $rsLista1;
    public $rsLista2;
    public $boCarregarDados;

    public function ISelectMultiploRecurso()
    {
        parent::SelectMultiplo();

        $this->setName   ('inCodRecurso');
        $this->setRotulo ( "Recursos" );
        $this->setTitle  ( "Selecione o(s) recurso(s)." );
        $this->setNull   ( true );

        $this->SetNomeLista1 ('inCodRecursoDisponivel');
        $this->setCampoId1   ( 'cod_recurso' );
        $this->setCampoDesc1 ( '[cod_recurso] - [nom_recurso]' );
        $this->setStyle1("width: 400px; height: 150px;");

        $this->SetNomeLista2 ('inCodRecursoSelecionado');
        $this->setCampoId2   ('cod_recurso');
        $this->setCampoDesc2 ('[cod_recurso] - [nom_recurso]');
        $this->setStyle2("width: 400px; height: 150px;");

        $this->setOrdenacao('value');
        $this->setRecordsetLista1( new RecordSet );
        $this->setRecordsetLista2( new RecordSet );
        $this->setCarregarDados( true );
        $this->setFiltro( "" );
    }

    public function setExercicio($stExercicio) { $this->stExercicio = $stExercicio; }
    public function setRecordsetLista1($rsLista1) { $this->rsLista1 = $rsLista1;       }
    public function setRecordsetLista2($rsLista2) { $this->rsLista2 = $rsLista2;       }
    public function setFiltro($stFiltro) { $this->stFiltro = $stFiltro;       }
    public function setCarregarDados($boCarregarDados) { $this->boCarregarDados = $boCarregarDados;       }

    public function getExercicio() { return $this->stExercicio; }
    public function getRecordsetLista1() { return $this->rsLista1; }
    public function getRecordsetLista2() { return $this->rsLista2; }
    public function getFiltro() { return $this->stFiltro; }
    public function getCarregarDados() { return $this->boCarregarDados; }

    public function montaRecordSet()
    {
        $rsRecordset = new RecordSet();
        $obTOrcamentoRecurso = new TOrcamentoRecurso();
        $obTOrcamentoRecurso->setDado('exercicio', $this->getExercicio());
        $obTOrcamentoRecurso->recuperaRelacionamento( $rsEntidadesGeral , $this->getFiltro(), 'recurso.cod_recurso' );
        $rsLista2 = $this->getRecordsetLista2();
        if ($rsEntidadesGeral->getNumLinhas() == 1 && $rsLista2->getNumLinhas() < 1 ) {
            $this->setRecordsetLista2( $rsEntidadesGeral );
            $this->setRecordsetLista1( new RecordSet );
        } else {
            $this->setRecordsetLista1( $rsEntidadesGeral );
        }
    }

    public function montaHTML()
    {
        if ($this->getCarregarDados()) {
          $this->montaRecordSet();
        }
        $this->SetRecord1( $this->getRecordsetLista1() );
        $this->SetRecord2( $this->getRecordsetLista2() );

        parent::montaHTML();
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente( $this );
    }
}
?>
