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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_IMOVEL
    * Data de Criação: 28/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaImovel.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.2  2006/09/29 17:16:22  dibueno
Alteração nos parametros dos campos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaImovel extends Persistente
{
    public $inExercicio    ;
    public $inCodInscricao ;
    public $inInscricaoMunicipal   ;

    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaImovel()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_imovel');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_inscricao, inscricao_municipal, exercicio');

        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('inscricao_municipal','integer',true,'',true,true);

        $this->inExercicio    		= '0';
        $this->inCodInscricao 		= '0';
        $this->inInscricaoMunicipal	= '0';

    }

    public function listaDividasEmAberto(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaDividasEmAberto().$stFiltro;
        $this->setDebug( $stSql );
        //$this->debug();exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaDividasEmAberto()
    {
        $stSql = "
            SELECT DISTINCT
                divida_cgm.cod_inscricao,
                divida_cgm.exercicio

            FROM
                divida.divida_imovel

            INNER JOIN
                divida.divida_cgm
            ON
                divida_cgm.cod_inscricao = divida_imovel.cod_inscricao
                AND divida_cgm.exercicio = divida_imovel.exercicio

            LEFT JOIN
                divida.divida_estorno
            ON
                divida_estorno.cod_inscricao = divida_imovel.cod_inscricao
                AND divida_estorno.exercicio = divida_imovel.exercicio

            LEFT JOIN
                divida.divida_cancelada
            ON
                divida_cancelada.cod_inscricao = divida_imovel.cod_inscricao
                AND divida_cancelada.exercicio = divida_imovel.exercicio

            LEFT JOIN
                divida.divida_remissao
            ON
                divida_remissao.cod_inscricao = divida_imovel.cod_inscricao
                AND divida_remissao.exercicio = divida_imovel.exercicio

            INNER JOIN
                (
                    SELECT
                        max(num_parcelamento) AS num_parcelamento,
                        cod_inscricao,
                        exercicio
                    FROM
                        divida.divida_parcelamento
                    GROUP BY
                        cod_inscricao,
                        exercicio
                )AS divida_parcelamento
            ON
                divida_parcelamento.cod_inscricao = divida_imovel.cod_inscricao
                AND divida_parcelamento.exercicio = divida_imovel.exercicio

            INNER JOIN
                divida.parcelamento
            ON
                parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento

            LEFT JOIN
                divida.parcela
            ON
                parcela.num_parcelamento = parcelamento.num_parcelamento
                AND paga = false
                AND cancelada = false

            LEFT JOIN
                divida.parcela AS tparcela
            ON
                tparcela.num_parcelamento = parcelamento.num_parcelamento

            WHERE
                divida_cancelada.cod_inscricao IS NULL
                AND divida_estorno.cod_inscricao IS NULL
                AND divida_remissao.cod_inscricao IS NULL
                AND CASE WHEN parcela.num_parcelamento IS NULL THEN
                        CASE WHEN tparcela.num_parcelamento IS NOT NULL THEN
                            false
                        ELSE
                            true
                        END
                    ELSE
                        true
                    END
        ";

        return $stSql;
    }
}// end of class

?>
