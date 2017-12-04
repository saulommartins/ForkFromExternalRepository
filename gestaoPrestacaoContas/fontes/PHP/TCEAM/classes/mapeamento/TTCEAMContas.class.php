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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 23/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMContas extends Persistente
{
    /*
    * Método Constructor
    * @access Private
    */
    public function TTCEAMContas()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaContas(&$rsRecordSet, $stCondicao = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaContas().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaContas()
    {
        $stSql = "
                SELECT *
                  FROM tceam.recupera_contas('".$this->getDado('exercicio')."', '".$this->getDado('cod_entidade')."', '".$this->getDado('mes')."')
                    AS retorno ( exercicio              VARCHAR
                               , cod_estrutural         VARCHAR
                               , cod_conta              INTEGER
                               , conta_contabil         VARCHAR
                               , nom_conta              VARCHAR
                               , nivel                  INTEGER
                               , recebe_lancamento      VARCHAR
                               , origem_saldo           VARCHAR
                               , conta_superior         VARCHAR
                               , cod_conta_reduzido     VARCHAR
                               , item_orcamentario      VARCHAR
                               , banco                  VARCHAR
                               , agencia                VARCHAR
                               , conta_corrente         VARCHAR
                               , tipo_conta             INTEGER
                               , cod_conta_tc           INTEGER
                     )
        ";

        return $stSql;
    }
}
?>
