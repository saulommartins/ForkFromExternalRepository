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
* Classe mapeamento tabela patrimonio.arquivo_coletora
*
*
* @date 11/08/2010
* @author Analista: Gelson
* @author Desenvol: Tonismar
*
* $Id: TPatrimonioArquivoColetora.class.php 66466 2016-08-31 14:34:38Z michel $
*
* @ignore
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";
include_once CLA_PERSISTENTE;

class TPatrimonioArquivoColetora extends Persistente
{
    public $transacao;

    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.arquivo_coletora');
        $this->setCampoCod('codigo');
        $this->addCampo('codigo',integer,true,'',true,false);
        $this->addCampo('nome','varchar',true,'27',false,false);
        $this->addCampo('md5sum','varchar',true,'35',false,false);
        $this->transacao = new Transacao();
    }

    public function recuperaMd5sum(&$rsRecordSet, $stFiltro = '', $boTransacao = '')
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMd5sum().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, '', $boTransacao );

        return $obErro;
    }

    public function recuperaNomeArquivo(&$rsRecordSet, $stFiltro = '', $boTransacao = '')
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaNomeArquivo().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, '', $boTransacao );

        return $obErro;
    }

    public function recuperaArquivosLocal(&$rsRecordSet, $stFiltro = '', $boTransacao = '')
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $rsRecordSet = new RecordSet;
        $ordem = 'GROUP BY arquivo_coletora.codigo, nome, md5sum';
        $stSql = $this->montaRecuperaArquivosLocal().$stFiltro.$ordem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, '', $boTransacao );

        return $obErro;
    }

    public function recuperaListaArquivo(&$rsRecordSet, $stFiltro = '', $boTransacao = '')
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaListaArquivo().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, '', $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMd5sum()
    {
        $stSql  = " SELECT                                   \n";
        $stSql .= "    md5sum                                \n";
        $stSql .= "   FROM                                   \n";
        $stSql .= "    patrimonio.arquivo_coletora           \n";
        $stSql .= "  WHERE                                   \n";
        $stSql .= "    md5sum = '".$this->getDado('md5sum')."' \n";

        return $stSql;
    }

    private function montaRecuperaNomeArquivo()
    {
        $stSql  = " SELECT                                  \n";
        $stSql .= "     nome                                \n";
        $stSql .= "   FROM                                  \n";
        $stSql .= "     patrimonio.arquivo_coletora         \n";
        $stSql .= "  WHERE                                  \n";
        $stSql .= "     nome = '".$this->getDado('nome')."' \n";

        return $stSql;
    }

    private function montaRecuperaListaArquivo()
    {
        $stSql = " SELECT
                 codigo
                ,nome
                ,md5sum
            FROM
                patrimonio.arquivo_coletora

        ";

        return $stSql;
    }

    private function montaRecuperaArquivosLocal()
    {
        $stSql  = "    SELECT                                                        \n";
        $stSql .= "         arquivo_coletora.codigo                                  \n";
        $stSql .= "        ,arquivo_coletora.nome                                    \n";
        $stSql .= "        ,arquivo_coletora.md5sum                                  \n";
        $stSql .= "      FROM                                                        \n";
        $stSql .= "        patrimonio.arquivo_coletora                               \n";
        $stSql .= "INNER JOIN                                                        \n";
        $stSql .= "        patrimonio.arquivo_coletora_dados                         \n";
        $stSql .= "        ON                                                        \n";
        $stSql .= "        arquivo_coletora.codigo = arquivo_coletora_dados.codigo   \n";

        return $stSql;
    }

    public function recuperaRelatorioConsistencia(&$rsRecordSet, $stFiltro = '', $boTransacao = '')
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRelatorioConsistencia().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, '', $boTransacao );

        return $obErro;
    }

     function montaRecuperaRelatorioConsistencia()
     {
        $stSql = " SELECT arquivo_coletora.codigo
                        , arquivo_coletora.nome
                     FROM patrimonio.arquivo_coletora

               INNER JOIN patrimonio.arquivo_coletora_dados
                       ON arquivo_coletora_dados.codigo = arquivo_coletora.codigo

               INNER JOIN patrimonio.arquivo_coletora_consistencia
                       ON arquivo_coletora_consistencia.codigo = arquivo_coletora_dados.codigo
                      AND arquivo_coletora_consistencia.num_placa = arquivo_coletora_dados.num_placa

                LEFT JOIN patrimonio.bem
                       ON bem.num_placa = arquivo_coletora_dados.num_placa

                LEFT JOIN patrimonio.bem_comprado
                       ON bem_comprado.cod_bem = bem.cod_bem
          ";

        return $stSql;
     }

}//class's end
