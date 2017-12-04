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
* Gerar o componente SelectMultiplo para Evento
* Data de Criação: 09/11/2005

* @author Analista: Dagiane	Vieira
* @author Desenvolvedor: Diego Lemos de Souza

* @package framework
* @subpackage componentes

    $Id: ISelectMultiploEvento.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-04.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

/**
    * Cria o componente BuscaInner para Evento
    * @author Desenvolvedor: Andre Almeida

    * @package framework
    * @subpackage componentes
*/
class ISelectMultiploEvento extends SelectMultiplo
{
    /**
        * @access Private
        * @var Object
    */
    public $obCmbEvento;
    public $boProventos;
    public $boDescontos;
    public $boBases;
    public $boInformativos;
    public $stOrdem;

    public function setProventos() {$this->boProventos=true;}
    public function setDescontos() {$this->boDescontos=true;}
    public function setBases() {$this->boBases=true;}
    public function setInformativos() {$this->boInformativos=true;}
    public function setOrdem($stOrdem) {$this->stOrdem=$stOrdem;}

    public function getProventos() {return $this->boProventos;}
    public function getDescontos() {return $this->boDescontos;}
    public function getBases() {return $this->boBases;}
    public function getInformativos() {return $this->boInformativos;}
    public function getOrdem() {return $this->stOrdem;}

    /**
        * Método Construtor
        * @access Public
    */
    public function ISelectMultiploEvento()
    {
        parent::SelectMultiplo();
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->recuperaEventos($rsEventos);

        $this->setName                           ( 'inCodEvento'                                             );
        $this->setRotulo                         ( "Eventos"                                                 );
        $this->setTitle                          ( "Selecione os eventos." );
        $this->SetNomeLista1                     ( 'inCodEventoDisponiveis'                                  );
        $this->setCampoId1                       ( '[cod_evento]'                                            );
        $this->setCampoDesc1                     ( '[codigo]-[descricao]'                                    );
        $this->setStyle1                         ( "width: 300px"                                            );
        $this->SetRecord1                        ( $rsEventos                                                );
        $this->SetNomeLista2                     ( 'inCodEventoSelecionados'                                 );
        $this->setCampoId2                       ( '[cod_evento]'                                            );
        $this->setCampoDesc2                     ( '[codigo]-[descricao]'                                    );
        $this->setStyle2                         ( "width: 300px"                                            );
        $this->SetRecord2                        ( new recordset                                             );
        //$this->obSelect1->setSize                ( 5                                                         );
        //$this->obSelect2->setSize                ( 5                                                         );
    }

    public function montarEventosDisponiveis()
    {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $stFiltro = " AND (";
        if ($this->getProventos()) {
            $stFiltro .= " natureza = 'P' OR";
        }
        if ($this->getDescontos()) {
            $stFiltro .= " natureza = 'D' OR";
        }
        if ($this->getBases()) {
            $stFiltro .= " natureza = 'B' OR";
        }
        if ($this->getInformativos()) {
            $stFiltro .= " natureza = 'I' OR";
        }
        if (trim($this->getOrdem()) != "") {
            $stOrdem = " ORDER BY ".$this->getOrdem();
        }
        $stFiltro = substr($stFiltro,0,strlen($stFiltro)-2).")";
        if ($this->getProventos() OR $this->getDescontos() OR $this->getBases() OR $this->getInformativos()) {
            $obTFolhaPagamentoEvento->recuperaEventos($rsEventos,$stFiltro,$stOrdem);
            $this->setRecord1($rsEventos);
        }
    }
}
?>
