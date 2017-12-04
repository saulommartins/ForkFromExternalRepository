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
   /*
    * Classe de Mapeamento da tabela tcems.despesas_nao_computadas
    * Data de Criação   : 25/07/2011

    * @author Desenvolvedor Davi Ritter Aroldi

    * @package URBEM
    * @subpackage

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TTCEMSDespesasNaoComputadas extends Persistente
{
    public function TTCEMSDespesasNaoComputadas()
    {
        parent::Persistente();
        $this->setTabela("tcems.despesas_nao_computadas");

        $this->setCampoCod('id');

        $this->AddCampo( 'id'                   , 'integer', true, ''    , true , false );
        $this->AddCampo( 'exercicio'            , 'char'   , true, '4'   , false, false );
        $this->AddCampo( 'descricao'            , 'char'   , true, '100' , false, false );
        $this->AddCampo( 'quadrimestre1'        , 'numeric', true, '14,2', false, false );
        $this->AddCampo( 'quadrimestre2'        , 'numeric', true, '14,2', false, false );
        $this->AddCampo( 'quadrimestre3'        , 'numeric', true, '14,2', false, false );
    }

    public function proximoId()
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = "SELECT max(id)+1 as proximoId from tcems.despesas_nao_computadas";
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $rsRecordSet->getCampo('proximoid') ? $rsRecordSet->getCampo('proximoid') : 1;
    }

    public function excluir($boTransacao = "")
    {
        $obErro     = new Erro;
        $obConexao  = new Conexao;

        if ( !$obErro->ocorreu() ) {
            $stSql = $this->montaExcluir();
            $this->setDebug( $stSql );
            $obErro = $obConexao->executaDML( $stSql, $boTransacao );
        }

        return $obErro;
    }

    public function montaExcluir()
    {
        $stSql = " DELETE FROM ".$this->getTabela()." WHERE";
        if ($this->getDado('id')) {
            $stSql .= " id = ".$this->getDado('id')."  AND";
        }
        if ($this->getDado('exercicio')) {
            $stSql .= " exercicio = '".$this->getDado('exercicio')."'  AND";
        }
        if ($this->getDado('descricao')) {
            $stSql .= " descricao = '".$this->getDado('descricao')."'  AND";
        }

        return substr($stSql, 0, -5);
    }
}
