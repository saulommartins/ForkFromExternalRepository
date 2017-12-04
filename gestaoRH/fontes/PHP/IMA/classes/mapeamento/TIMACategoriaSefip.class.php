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
    * Classe de mapeamento da tabela ima.categoria_sefip
    * Data de Criação: 07/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TIMACategoriaSefip.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.categoria_sefip
  * Data de Criação: 07/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMACategoriaSefip extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMACategoriaSefip()
{
    parent::Persistente();
    $this->setTabela("ima.categoria_sefip");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_categoria,cod_modalidade');

    $this->AddCampo('cod_categoria' ,'integer',true  ,'',true,'TPessoalCategoria');
    $this->AddCampo('cod_modalidade','integer',true  ,'',true,'TIMAModalidadeRecolhimento');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT categoria_sefip.*                                                             \n";
    $stSql .= "     , modalidade_recolhimento.sefip                                                 \n";
    $stSql .= "     , modalidade_recolhimento.descricao                                             \n";
    $stSql .= "  FROM ima.categoria_sefip                                 \n";
    $stSql .= "     , ima.modalidade_recolhimento                         \n";
    $stSql .= " WHERE categoria_sefip.cod_modalidade = modalidade_recolhimento.cod_modalidade       \n";

    return $stSql;
}

function recuperaCategoriasContratos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaCategoriaContratos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaCategoriaContratos()
{
    $stSql .= "SELECT categoria_sefip.*                                                             \n";
    $stSql .= "     , modalidade_recolhimento.sefip                                                 \n";
    $stSql .= "     , modalidade_recolhimento.descricao                                             \n";
    $stSql .= "     , contrato_servidor.cod_contrato                                                \n";
    $stSql .= "  FROM ima.categoria_sefip                                 \n";
    $stSql .= "     , ima.modalidade_recolhimento                         \n";
    $stSql .= "     , pessoal.contrato_servidor                           \n";
    $stSql .= " WHERE categoria_sefip.cod_modalidade = modalidade_recolhimento.cod_modalidade       \n";
    $stSql .= "   AND categoria_sefip.cod_categoria = contrato_servidor.cod_categoria               \n";

    return $stSql;
}

function recuperaModalidades(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaModalidades",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaModalidades()
{
    $stSql .= "SELECT modalidade_recolhimento.sefip                                                 \n";
    $stSql .= "     , modalidade_recolhimento.cod_modalidade                                        \n";
    $stSql .= "  FROM ima.categoria_sefip                                 \n";
    $stSql .= "     , ima.modalidade_recolhimento                         \n";
    $stSql .= "     , pessoal.contrato_servidor                           \n";
    $stSql .= " WHERE categoria_sefip.cod_modalidade = modalidade_recolhimento.cod_modalidade       \n";
    $stSql .= "   AND categoria_sefip.cod_categoria = contrato_servidor.cod_categoria               \n";
    $stSql .= "GROUP BY modalidade_recolhimento.sefip                                               \n";
    $stSql .= "       , modalidade_recolhimento.cod_modalidade                                      \n";

    return $stSql;
}

}
?>
