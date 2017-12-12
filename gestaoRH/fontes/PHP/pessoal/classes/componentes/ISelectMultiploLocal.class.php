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
* Gerar o componente o SelectMultiplo com o Local
* Data de Criação: 09/11/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package beneficios
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php" );

/**
    * Cria o componente SelectMultiplo com o Local
    * @author Desenvolvedor: Andre Almeida

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploLocal extends SelectMultiplo
{
    /**
     * @access Private
     * @var Object
     */
    var $obROrganogramaLocal;

    var $rsDisponiveis;
    var $rsSelecionados;

    public function setDisponiveis($value) { $this->rsDisponiveis = $value; }
    public function getDisponiveis()       { return $this->rsDisponiveis; }

    public function setSelecionados($value) { $this->rsSelecionados = $value; }
    public function getSelecionados()       { return $this->rsSelecionados; }

    /**
     * @access Public
     * @Param Object $valor
     */
    function setROrganogramaLocal($valor) { $this->obROrganogramaLocal = $valor; }

    /**
     * @access Public
     * @return Object
     */
    function getROrganogramaLocal() { return $this->obROrganogramaLocal; }

    /**
     * Método Construtor
     * @access Public
     */
    public function ISelectMultiploLocal()
    {
        parent::SelectMultiplo();

        $this->setROrganogramaLocal ( new ROrganogramaLocal );
        $this->obROrganogramaLocal->listarLocal( $rsDisponiveis );
        $rsSelecionados = new Recordset;

        $this->setName       ( "inCodLocal"                    );
        $this->setRotulo     ( "Local"                         );
        $this->setTitle      ( "Informe o local para o filtro" );
        $this->setNomeLista1 ( "inCodLocalDisponiveis"         );
        $this->setRecord1    ( $rsDisponiveis                  );
        $this->setCampoId1   ( "[cod_local]"                   );
        $this->setCampoDesc1 ( "[cod_local] - [descricao]"     );
        $this->setStyle1     ( "width:300px"                   );
        $this->setNomeLista2 ( "inCodLocalSelecionados"        );
        $this->setRecord2    ( $rsSelecionados                 );
        $this->setCampoId2   ( "[cod_local]"                   );
        $this->setCampoDesc2 ( "[cod_local] - [descricao]"     );
        $this->setStyle2     ( "width:300px"                   );
    }

    public function montaHtml()
    {
        # Alterado o componente para retirar dos disponíveis os locais selecionados.
        if ($this->getDisponiveis()) {
            $this->setRecord1($this->getDisponiveis());
        } 
    
        # Alterado o componente para apresentar os locais selecionados.
        if ($this->getSelecionados()) {
            $this->setRecord2($this->getSelecionados());
        }
    
       parent::montaHtml();
    }
}

?>
