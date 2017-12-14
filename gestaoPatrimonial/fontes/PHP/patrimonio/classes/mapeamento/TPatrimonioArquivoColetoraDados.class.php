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
    * Classe mapeamento tabela patrimonio.arquivo_coletora_dados
    *
    *
    * @date 10/08/2010
    * @author Analista: Gelson
    * @author Desenvol: Tonismar
    *
    * @ignore
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
include_once(CLA_PERSISTENTE);

class TPatrimonioArquivoColetoraDados extends Persistente
{
    public $transacao;

    public function TPatrimonioArquivoColetoraDados()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.arquivo_coletora_dados');
        $this->setCampoCod('codigo');
        $this->setComplementoChave('num_placa');
        $this->addCampo('cod_local','integer',true,'',false,true);
        $this->addCampo('codigo','integer',true,'',true,true);
        $this->addCampo('num_placa','varchar',true,'20',true,false);
        $this->transacao = new Transacao();
    }

    public function recuperaPlaca(&$recordSet)
    {
        $e = new Erro;
        $conexao = new Conexao;
        $recordSet = new RecordSet;
        $sql = $this->montaConsultaPlaca();
        $this->stDebug = $sql;
        $e = $conexao->executaSQL( $recordSet, $sql, '', $trans='' );

        return $e;
    }

    public function recuperaPlacaUrbem(&$recordSet)
    {
        $e = new Erro;
        $conexao = new Conexao;
        $recordSet = new RecordSet;
        $sql = $this->montaConsultaPlacaUrbem();
        $this->stDebug = $sql;
        $e = $conexao->executaSQL( $recordSet, $sql, '', $trans='' );

        return $e;
    }

    private function montaConsultaPlacaUrbem()
    {
        $stSql = " SELECT
                        num_placa
                     FROM
                        patrimonio.bem
                    WHERE NOT EXISTS (
                            SELECT
                                num_placa
                            FROM
                                patrimonio.arquivo_coletora_dados
                            WHERE
                                bem.num_placa = arquivo_coletora_dados.num_placa
                        )
                        AND num_placa <> '' ";

        return $stSql;
    }

    private function montaConsultaPlaca()
    {

        $stSql = "SELECT
                     historico_bem.cod_local
                    ,historico_bem.cod_bem
                  FROM
                    patrimonio.historico_bem
            INNER JOIN
                (
                        SELECT
                             bem.cod_bem
                            ,max(timestamp) as timestamp
                          FROM
                            patrimonio.historico_bem
                    INNER JOIN
                            patrimonio.bem
                            ON
                            historico_bem.cod_bem = bem.cod_bem
                         WHERE
                            num_placa = '".$this->getDado('num_placa')."'
                      GROUP BY
                            bem.cod_bem
                      ORDER BY
                            bem.cod_bem
                ) AS bem
                  ON
                    bem.cod_bem = historico_bem.cod_bem
                    AND bem.timestamp = historico_bem.timestamp
            ";

        return $stSql;
    }

    private function montaConsultaPlacaa()
    {
        $stSql = "select
                         sw.cod_local as local_sw
                        ,sw.num_placa as num_placa_sw
                        ,arquivo_coletora_dados.num_placa as num_placa_file
                        ,arquivo_coletora_dados.cod_local as local_file
                    from
                        (select
                             cod_local
                            ,num_placa
                        from
                            patrimonio.bem
                        inner join
                            (select
                                 cod_bem
                                ,cod_local
                                ,max(timestamp) as timestamp
                             from
                                patrimonio.historico_bem
                             group by cod_bem, cod_local
                            ) as historico_bem
                        on
                            historico_bem.cod_bem = bem.cod_bem ) as sw
                    left join
                    patrimonio.arquivo_coletora_dados
                    on
                    arquivo_coletora_dados.num_placa = sw.num_placa
        ";

        return $stSql;
    }
    public function recuperaDadosConsistencia(&$recordSet)
    {
        $obErro  = new Erro;
        $conexao = new Conexao;
        $recordSet = new RecordSet;
        $sql = $this->montaRecuperaDadosConsistencia();
        $this->stDebug = $sql;
        $obErro  = $conexao->executaSQL( $recordSet, $sql, '', $trans='' );

        return $obErro;
    }

    private function montaRecuperaDadosConsistencia()
    {

        $stSql = "SELECT num_placa, codigo
                  FROM patrimonio.arquivo_coletora_dados
                                WHERE   codigo = '".$this->getDado('codigo')."'

                            ";

        return $stSql;
    }

} //class's end
