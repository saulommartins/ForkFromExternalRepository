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
    * Classe de mapeamento da tabela ARRECADACAO.NOTA_SERVICO
    * Data de Criação: 23/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRNotaServico.class.php 59612 2014-09-02 12:00:51Z gelson $

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

class TARRNotaServico extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRNotaServico()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.nota_servico');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_nota,cod_atividade,cod_servico,inscricao_economica,ocorrencia,timestamp');

        $this->AddCampo('cod_nota', 'integer', true, '', true, true );
        $this->AddCampo('cod_atividade', 'integer', true, '', true, true );
        $this->AddCampo('cod_servico', 'integer', true, '', true, true );
        $this->AddCampo('inscricao_economica', 'integer', true, '', true, true  );
        $this->AddCampo('ocorrencia', 'integer', true, '', true, true  );
        $this->AddCampo('timestamp', 'timestamp', false, '', true, true );

    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL .= " SELECT                                                                              \n";
        $stSQL .= "     nota.cod_nota                                                                   \n";
        $stSQL .= "     ,nota.nro_serie                                                                 \n";
        $stSQL .= "     ,nota.nro_nota                                                                  \n";
        $stSQL .= "     ,nota.valor_mercadoria                                                          \n";
        $stSQL .= "     ,nota.valor_nota                                                                \n";
        $stSQL .= "     ,to_char(faturamento_servico.dt_emissao,'dd/mm/yyyy') as dt_emissao             \n";
        $stSQL .= "     ,faturamento_servico.cod_servico                                                \n";
        $stSQL .= "     ,faturamento_servico.ocorrencia                                                 \n";
        $stSQL .= " FROM                                                                                \n";
        $stSQL .= "     arrecadacao.nota,                                                               \n";
        $stSQL .= "     arrecadacao.nota_servico,                                                       \n";
        $stSQL .= "     arrecadacao.faturamento_servico                                                 \n";
        $stSQL .= " WHERE                                                                               \n";
        $stSQL .= "     nota.cod_nota = nota_servico.cod_nota                                           \n";
        $stSQL .= "     AND nota_servico.cod_atividade = faturamento_servico.cod_atividade              \n";
        $stSQL .= "     AND nota_servico.cod_servico = faturamento_servico.cod_servico                  \n";
        $stSQL .= "     AND nota_servico.inscricao_economica = faturamento_servico.inscricao_economica  \n";
        $stSQL .= "     AND nota_servico.ocorrencia = faturamento_servico.ocorrencia                    \n";
        $stSQL .= "     AND nota_servico.timestamp = faturamento_servico.timestamp                      \n";

        return $stSQL;
    }

    public function recuperaNotasServico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaNotasServico($stFiltro).$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNotasServico($stFiltro)
    {
        $stSQL .= " SELECT                                                                              \n";
        $stSQL .= "      nota.cod_nota                                                                  \n";
        $stSQL .= "      ,nota.nro_serie                                                                \n";
        $stSQL .= "      ,nota.nro_nota                                                                 \n";
        $stSQL .= "      ,nota.valor_mercadoria                                                         \n";
        $stSQL .= "      ,nota.valor_nota                                                               \n";
        $stSQL .= "      ,to_char(faturamento_servico.dt_emissao,'dd/mm/yyyy') as dt_emissao            \n";
        $stSQL .= "  FROM                                                                               \n";
        $stSQL .= "      arrecadacao.nota,                                                              \n";
        $stSQL .= "      arrecadacao.nota_servico,                                                      \n";
        $stSQL .= "      arrecadacao.faturamento_servico                                                \n";
        $stSQL .= "  WHERE                                                                              \n";
        $stSQL .= "      nota.cod_nota = nota_servico.cod_nota                                          \n";
        $stSQL .= "      AND nota_servico.cod_atividade = faturamento_servico.cod_atividade             \n";
        $stSQL .= "      AND nota_servico.inscricao_economica = faturamento_servico.inscricao_economica \n";
        $stSQL .= "      AND nota_servico.ocorrencia = faturamento_servico.ocorrencia                   \n";
        $stSQL .= "      AND nota_servico.timestamp = faturamento_servico.timestamp                     \n";
        $stSQL .= $stFiltro;
        $stSQL .= " GROUP BY                                                                            \n";
        $stSQL .= "      nota.cod_nota                                                                  \n";
        $stSQL .= "      ,nota.nro_serie                                                                \n";
        $stSQL .= "      ,nota.nro_nota                                                                 \n";
        $stSQL .= "      ,nota.valor_mercadoria                                                         \n";
        $stSQL .= "      ,nota.valor_nota                                                               \n";
        $stSQL .= "      ,faturamento_servico.dt_emissao;                                               \n";

        return $stSQL;
    }

}
?>
