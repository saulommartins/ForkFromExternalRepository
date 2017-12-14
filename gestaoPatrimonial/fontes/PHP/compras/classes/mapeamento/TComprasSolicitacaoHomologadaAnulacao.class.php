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
 * Classe de mapeamento da tabela compras.solicitacao_homologada_anulacao
 * Data de Criação: 08/04/2008

 * @author Analista:  Gelson W
 * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TComprasSolicitacaoHomologadaAnulacao.class.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  compras.solicitacao_homologada
  * Data de Criação: 30/06/2006

  * @author Analista: Gelson W
  * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasSolicitacaoHomologadaAnulacao extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TComprasSolicitacaoHomologadaAnulacao()
    {
        parent::Persistente();
        $this->setTabela("compras.solicitacao_homologada_anulacao");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,cod_solicitacao');

        $this->AddCampo('exercicio'       , 'CHAR(4)'   , true  , '' , true  , true);
        $this->AddCampo('cod_entidade'    , 'INTEGER'   , true  , '' , true  , true);
        $this->AddCampo('cod_solicitacao' , 'INTEGER'   , true  , '' , true  , true);
        $this->AddCampo('numcgm'          , 'INTEGER'   , true  , '' , false , true);
        $this->AddCampo('timestamp'       , 'TIMESTAMP' , false , '' , false , false);
    }

    public function verificaExistenciaHomologacaoAnulada(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaVerificaExistenciaHomologacaoAnulada",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaVerificaExistenciaHomologacaoAnulada()
    {
        $stSql = " SELECT 1 as EXISTE \n ";
        $stSql .="   FROM compras.solicitacao_homologada_anulacao \n ";
        $stSql .="  WHERE solicitacao_homologada_anulacao.cod_solicitacao =".$this->getDado('cod_solicitacao')."\n ";
        $stSql .="    AND solicitacao_homologada_anulacao.cod_entidade =".$this->getDado('cod_entidade')."\n ";
        $stSql .="    AND solicitacao_homologada_anulacao.exercicio ='".$this->getDado('exercicio')."'\n ";

        return $stSql;
    }
}

?>
