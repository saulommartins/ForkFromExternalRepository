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
    * Classe de componente ISelectMultiplo
    * Data de Criação: 07/04/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.00.00

    $Id: ISelectMultiploCategoria.class.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

/**
    * Cria o componente SelectMultiplo com o Categoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploCategoria extends SelectMultiplo
{
/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploCategoria()
{
    parent::SelectMultiplo();

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCategoria.class.php");
    $obTPessoalCategoria = new TPessoalCategoria();
    $obTPessoalCategoria->recuperaTodos($rsDisponiveis);
    $rsSelecionados = new Recordset;

    $this->setName       ( "inCodCategoria"                    );
    $this->setRotulo     ( "Categoria"                         );
    $this->setTitle      ( "Informe o categoria para o filtro" );
    $this->setNomeLista1 ( "inCodCategoriaDisponiveis"         );
    $this->setRecord1    ( $rsDisponiveis                  );
    $this->setCampoId1   ( "[cod_categoria]"                   );
    $this->setCampoDesc1 ( "[cod_categoria] - [descricao]"     );
    $this->setStyle1     ( "width:300px"                   );
    $this->setNomeLista2 ( "inCodCategoriaSelecionados"        );
    $this->setRecord2    ( $rsSelecionados                 );
    $this->setCampoId2   ( "[cod_categoria]"                   );
    $this->setCampoDesc2 ( "[cod_categoria] - [descricao]"     );
    $this->setStyle2     ( "width:300px"                   );
}

}

?>
