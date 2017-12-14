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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_ESTORNO
    * Data de Criação: 02/08/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaEstorno.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.2  2007/08/15 21:18:39  cercato
alterando funcionamento do estorno.

Revision 1.1  2007/08/02 19:36:29  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATDividaEstorno extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaEstorno()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_estorno');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_inscricao');

        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('motivo','varchar',true,'80',false,false);
        $this->AddCampo('timestamp','timestamp',false,'',false,false);
    }

    public function RecuperaInscricoesPagas(&$rsRecordSet, $stInscricao, $stExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInscricoesPagas( $stInscricao, $stExercicio );
        $this->setDebug( $stSql );
        //$this->debug(); #exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaInscricoesPagas($stInscricao, $stExercicio)
    {
        $stSql = "  SELECT
                        ddp.num_parcelamento,
                        (
                            SELECT
                                count(dp)
                            FROM
                                divida.parcela AS dp
                            WHERE
                                dp.num_parcelamento = ddp.num_parcelamento
                                AND dp.paga = true
                        )AS total_paga,
                        (
                            SELECT
                                count(dp)
                            FROM
                                divida.parcela AS dp
                            WHERE
                                dp.num_parcelamento = ddp.num_parcelamento
                                AND dp.paga = false
                                AND dp.cancelada = true
                        )AS total_cancelada,
                        (
                            SELECT
                                count(dp)
                            FROM
                                divida.parcela AS dp
                            WHERE
                                dp.num_parcelamento = ddp.num_parcelamento
                        )AS total_geral
                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_parcelamento AS ddp
                    ON
                        ddp.cod_inscricao = dda.cod_inscricao
                        AND ddp.exercicio = dda.exercicio

                    WHERE
                        dda.cod_inscricao = ".$stInscricao."
                        AND dda.exercicio = '".$stExercicio."'

                    GROUP BY
                        ddp.num_parcelamento";

        return $stSql;
    }

    public function RecuperaNumeracaoCarnesCanceladosDivida(&$rsRecordSet, $stInscricao, $stExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNumeracaoCarnesCanceladosDivida( $stInscricao, $stExercicio );
        $this->setDebug( $stSql );
        //$this->debug(); #exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNumeracaoCarnesCanceladosDivida($stInscricao, $stExercicio)
    {
        $stSql = "  SELECT DISTINCT
                        acd.numeracao,
                        acd.timestamp

                    FROM
                        arrecadacao.parcela AS apar

                    INNER JOIN
                        arrecadacao.carne AS ac
                    ON
                        apar.cod_parcela = ac.cod_parcela

                    INNER JOIN
                        arrecadacao.carne_devolucao AS acd
                    ON
                        acd.numeracao = ac.numeracao
                        AND acd.cod_motivo = 11

                    WHERE
                        apar.cod_lancamento IN (
                            SELECT DISTINCT
                                ap.cod_lancamento

                            FROM
                                divida.divida_ativa AS dda

                            INNER JOIN
                                divida.divida_parcelamento AS ddp
                            ON
                                ddp.cod_inscricao = dda.cod_inscricao
                                AND ddp.exercicio = dda.exercicio

                            INNER JOIN
                                divida.parcela_origem AS dpo
                            ON
                                dpo.num_parcelamento = ddp.num_parcelamento

                            INNER JOIN
                                arrecadacao.parcela AS ap
                            ON
                                ap.cod_parcela = dpo.cod_parcela

                            WHERE
                                dda.cod_inscricao = ".$stInscricao."
                                AND dda.exercicio = '".$stExercicio."'
                        ) ";

        return $stSql;
    }

}// end of class

?>
