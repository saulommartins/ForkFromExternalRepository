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
    * Classe de mapeamento da tabela pessoal.lote_ferias_contrato
    * Data de Criação: 07/05/2009

    * @author Desenvolvedor: Alex Cardoso

    * Casos de uso: uc-tabelas

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.lote_ferias_contrato
  * Data de Criação: 07/05/2009

  * @author Desenvolvedor: Alex Cardoso

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalLoteFeriasContrato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalLoteFeriasContrato()
{
    parent::Persistente();
    $this->setTabela("pessoal.lote_ferias_contrato");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,cod_contrato');

    $this->AddCampo('cod_lote'    ,'integer' ,true  ,''  ,true ,"TPessoalLoteFerias");
    $this->AddCampo('cod_contrato','integer' ,true  ,''  ,true ,"TPessoalContrato");

}

}
?>
