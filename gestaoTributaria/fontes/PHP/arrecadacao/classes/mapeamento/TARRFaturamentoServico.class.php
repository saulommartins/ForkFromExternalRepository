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
    * Classe de mapeamento da tabela ARRECADACAO.FATURAMENTO_SERVICO
    * Data de Criação: 23/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRFaturamentoServico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.22
*/

/*
$Log$
Revision 1.2  2007/02/22 12:16:35  cassiano
Alteração na escrituração de receita, inclusão do campo ocorrencia em algumas tabelas.

Revision 1.1  2006/10/26 14:06:43  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRFaturamentoServico extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRFaturamentoServico()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.faturamento_servico');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_atividade,cod_servico,inscricao_economica,timestamp,cod_modalidade,ocorrencia');

        $this->AddCampo('cod_atividade', 'integer', true, '', true, true );
        $this->AddCampo('cod_servico', 'integer', true, '', true, true );
        $this->AddCampo('inscricao_economica', 'integer', true, '', true, true  );
        $this->AddCampo('ocorrencia', 'integer', true, '', true, false  );
        $this->AddCampo('timestamp', 'timestamp', false, '', true, true );
        $this->AddCampo('cod_modalidade', 'integer', true, '', false, true );
        $this->AddCampo('dt_emissao', 'date', true, '', false, false );
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = " SELECT                                                                                      \n";
        $stSQL .= "     faturamento_servico.cod_atividade                                                       \n";
        $stSQL .= "     ,servico.cod_servico                                                                    \n";
        $stSQL .= "     ,servico.nom_servico                                                                    \n";
        $stSQL .= "     ,COALESCE( servico_com_retencao.valor_retido,0.00) AS valor_retido                         \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.valor_declarado,0.00) AS valor_declarado                    \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.valor_deducao,0.00) AS valor_deducao                        \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.valor_lancado,0.00) AS valor_lancado                        \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.aliquota,0.00) AS aliquota                                  \n";
        $stSQL .= " FROM                                                                                        \n";
        $stSQL .= "     arrecadacao.faturamento_servico                                                         \n";
        $stSQL .= " INNER JOIN                                                                                  \n";
        $stSQL .= "     economico.servico                                                                       \n";
        $stSQL .= " ON                                                                                          \n";
        $stSQL .= "     servico.cod_servico = faturamento_servico.cod_servico                                   \n";
        $stSQL .= " LEFT JOIN                                                                                   \n";
        $stSQL .= "     arrecadacao.servico_sem_retencao                                                        \n";
        $stSQL .= " ON                                                                                          \n";
        $stSQL .= "     faturamento_servico.cod_atividade           = servico_sem_retencao.cod_atividade        \n";
        $stSQL .= "     AND faturamento_servico.cod_servico         = servico_sem_retencao.cod_servico          \n";
        $stSQL .= "     AND faturamento_servico.inscricao_economica = servico_sem_retencao.inscricao_economica  \n";
        $stSQL .= "     AND faturamento_servico.ocorrencia          = servico_sem_retencao.ocorrencia           \n";
        $stSQL .= "     AND faturamento_servico.timestamp           = servico_sem_retencao.timestamp            \n";
        $stSQL .= " LEFT JOIN                                                                                   \n";
        $stSQL .= "     arrecadacao.servico_com_retencao                                                        \n";
        $stSQL .= " ON                                                                                          \n";
        $stSQL .= "     faturamento_servico.cod_atividade           = servico_com_retencao.cod_atividade        \n";
        $stSQL .= "     AND faturamento_servico.cod_servico         = servico_com_retencao.cod_servico          \n";
        $stSQL .= "     AND faturamento_servico.inscricao_economica = servico_com_retencao.inscricao_economica  \n";
        $stSQL .= "     AND faturamento_servico.ocorrencia          = servico_com_retencao.ocorrencia           \n";
        $stSQL .= "     AND faturamento_servico.timestamp           = servico_com_retencao.timestamp            \n";

        return $stSQL;
    }

    public function recuperaRelacionamentoNota(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoNota().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoNota()
    {
        $stSQL  = " SELECT                                                                                      \n";
        $stSQL .= "     faturamento_servico.cod_atividade                                                       \n";
        $stSQL .= "     ,servico.cod_servico                                                                    \n";
        $stSQL .= "     ,servico.nom_servico                                                                    \n";
        $stSQL .= "     ,COALESCE( servico_com_retencao.valor_retido,0.00) AS valor_retido                      \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.valor_declarado,0.00) AS valor_declarado                 \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.valor_deducao,0.00) AS valor_deducao                     \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.valor_lancado,0.00) AS valor_lancado                     \n";
        $stSQL .= "     ,COALESCE(servico_sem_retencao.aliquota,0.00) AS aliquota                               \n";
        $stSQL .= "     ,nota_servico.cod_nota                                                                  \n";
        $stSQL .= " FROM                                                                                        \n";
        $stSQL .= "     arrecadacao.faturamento_servico                                                         \n";
        $stSQL .= " INNER JOIN                                                                                  \n";
        $stSQL .= "     economico.servico                                                                       \n";
        $stSQL .= " ON                                                                                          \n";
        $stSQL .= "     servico.cod_servico = faturamento_servico.cod_servico                                   \n";

        $stSQL .= " INNER JOIN                                                                                  \n";
        $stSQL .= "     arrecadacao.nota_servico                                                                \n";
        $stSQL .= " ON                                                                                          \n";
        $stSQL .= "     faturamento_servico.cod_atividade           = nota_servico.cod_atividade                \n";
        $stSQL .= "     AND faturamento_servico.cod_servico         = nota_servico.cod_servico                  \n";
        $stSQL .= "     AND faturamento_servico.inscricao_economica = nota_servico.inscricao_economica          \n";
        $stSQL .= "     AND faturamento_servico.ocorrencia          = nota_servico.ocorrencia                   \n";
        $stSQL .= "     AND faturamento_servico.timestamp           = nota_servico.timestamp                    \n";

        $stSQL .= " LEFT JOIN                                                                                   \n";
        $stSQL .= "     arrecadacao.servico_sem_retencao                                                        \n";
        $stSQL .= " ON                                                                                          \n";
        $stSQL .= "     faturamento_servico.cod_atividade           = servico_sem_retencao.cod_atividade        \n";
        $stSQL .= "     AND faturamento_servico.cod_servico         = servico_sem_retencao.cod_servico          \n";
        $stSQL .= "     AND faturamento_servico.inscricao_economica = servico_sem_retencao.inscricao_economica  \n";
        $stSQL .= "     AND faturamento_servico.ocorrencia          = servico_sem_retencao.ocorrencia           \n";
        $stSQL .= "     AND faturamento_servico.timestamp           = servico_sem_retencao.timestamp            \n";
        $stSQL .= " LEFT JOIN                                                                                   \n";
        $stSQL .= "     arrecadacao.servico_com_retencao                                                        \n";
        $stSQL .= " ON                                                                                          \n";
        $stSQL .= "     faturamento_servico.cod_atividade           = servico_com_retencao.cod_atividade        \n";
        $stSQL .= "     AND faturamento_servico.cod_servico         = servico_com_retencao.cod_servico          \n";
        $stSQL .= "     AND faturamento_servico.inscricao_economica = servico_com_retencao.inscricao_economica  \n";
        $stSQL .= "     AND faturamento_servico.ocorrencia          = servico_com_retencao.ocorrencia           \n";
        $stSQL .= "     AND faturamento_servico.timestamp           = servico_com_retencao.timestamp            \n";

        return $stSQL;
    }
}
?>
