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
    * Classe de mapeamento da tabela pessoal.pensionista
    * Data de Criação: 15/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-01-16 10:43:35 -0200 (Qua, 16 Jan 2008) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.pensionista
  * Data de Criação: 15/08/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/

class TPessoalPensionista extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalPensionista()
{
    parent::Persistente();
    $this->setTabela("pessoal.pensionista");

    $this->setCampoCod('cod_pensionista');
    $this->setComplementoChave('cod_contrato_cedente');

    $this->AddCampo('cod_pensionista','sequence',true,'',true,false);
    $this->AddCampo('numcgm','integer',true,'',false,"TCGMPessoaFisica");
    $this->AddCampo('cod_contrato_cedente','integer',true,'',true,"TPessoalContratoServidor","cod_contrato");
    $this->AddCampo('cod_grau','integer',true,'',false,"TCGMGrauParentesco");
    $this->AddCampo('cod_profissao','integer',true,'',false,"TCSEProfissao");

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT contrato_pensionista.*                                                        \n";
    $stSql .= "  FROM pessoal.contrato_pensionista                                         \n";
    $stSql .= "     , pessoal.pensionista                                                  \n";
    $stSql .= " WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista   \n";
    $stSql .= "   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente   \n";

    return $stSql;
}

function recuperaCgmDoRegistro(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.registro ";
    $stSql = $this->montaRecuperaCgmDoRegistro().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCgmDoRegistro()
{
    $stSql .= "SELECT sw_cgm.numcgm                                                        \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                       \n";
    $stSql .= "     , contrato.*                                                           \n";
    $stSql .= "     , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "  FROM pessoal.contrato                                                     \n";
    $stSql .= "     , pessoal.contrato_pensionista                                         \n";
    $stSql .= "     , pessoal.pensionista                                                  \n";
    $stSql .= "     , sw_cgm                                                               \n";
    $stSql .= " WHERE contrato.cod_contrato = contrato_pensionista.cod_contrato            \n";
    $stSql .= "   AND contrato_pensionista.cod_pensionista = pensionista.cod_pensionista   \n";
    $stSql .= "   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente   \n";
    $stSql .= "   AND pensionista.numcgm = sw_cgm.numcgm                                   \n";

    return $stSql;
}

function recuperaPensaoMorteEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaPensaoMorteEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaPensaoMorteEsfinge()
{
    $stSql = "

                select sw_processo.cod_processo
                     , to_char( sw_processo.timestamp, 'dd/mm/yyyy' ) as timestamp
                     , contrato.registro
                     , causa_obito.num_certidao_obito
                     , valor_pensao.valor
                     , sw_processo.resumo_assunto
                  from pessoal.contrato_pensionista

                  join pessoal.contrato_pensionista_processo
                    on contrato_pensionista_processo.cod_contrato = contrato_pensionista.cod_contrato

                  join sw_processo
                    on sw_processo.ano_exercicio = contrato_pensionista_processo.ano_exercicio
                   and sw_processo.cod_processo  = contrato_pensionista_processo.cod_processo

                  join pessoal.contrato
                    on contrato.cod_contrato = contrato_pensionista.cod_contrato

                  join pessoal.causa_obito
                    on causa_obito.cod_contrato = contrato.cod_contrato

                  join (
                            select registro_evento_periodo.cod_contrato
                                    , sum(registro_evento.valor) as valor
                                from folhapagamento.registro_evento

                                join folhapagamento.registro_evento_periodo
                                on registro_evento_periodo.cod_registro = registro_evento.cod_registro

                                join (
                                        select cod_contrato
                                                , max( cod_periodo_movimentacao ) as max_cod_periodo_movimentacao
                                            from folhapagamento.registro_evento_periodo
                                        group by cod_contrato
                                    ) as ultimo_periodo_contrato
                                on ultimo_periodo_contrato.cod_contrato = registro_evento_periodo.cod_contrato
                                and ultimo_periodo_contrato.max_cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao

                            group by registro_evento_periodo.cod_contrato
                       ) as valor_pensao
                    on valor_pensao.cod_contrato = contrato.cod_contrato

                 where contrato_pensionista.dt_inicio_beneficio <= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
                   and contrato_pensionista.dt_encerramento >= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
                    or contrato_pensionista.dt_encerramento is null
    ";

    return $stSql;

}

function recuperaBeneficiarioPensaoEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="order by contrato.registro", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaBeneficiarioPensaoEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaBeneficiarioPensaoEsfinge()
{
    $stSql = "
                select sw_processo.cod_processo
                     , contrato.registro
                     , to_char( sw_processo.timestamp, 'dd/mm/yyyy' ) as timestamp
                     , sw_cgm.nom_cgm
                     , contrato.cod_contrato
                     , contrato_pensionista.percentual_pagamento * valor_pensao.valor as valor
                  from pessoal.pensionista

                  join pessoal.contrato_pensionista
                    on contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                   and contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente

                  join pessoal.contrato_pensionista_processo
                    on contrato_pensionista_processo.cod_contrato = contrato_pensionista.cod_contrato

                  join sw_processo
                    on sw_processo.ano_exercicio = contrato_pensionista_processo.ano_exercicio
                   and sw_processo.cod_processo  = contrato_pensionista_processo.cod_processo

                  join pessoal.contrato
                    on contrato.cod_contrato = contrato_pensionista.cod_contrato

                  join sw_cgm
                    on sw_cgm.numcgm = pensionista.numcgm

                  join (
                            select registro_evento_periodo.cod_contrato
                                 , sum(registro_evento.valor) as valor
                              from folhapagamento.registro_evento

                              join folhapagamento.registro_evento_periodo
                                on registro_evento_periodo.cod_registro = registro_evento.cod_registro

                              join (
                                        select cod_contrato
                                             , max( cod_periodo_movimentacao ) as max_cod_periodo_movimentacao
                                          from folhapagamento.registro_evento_periodo
                                      group by cod_contrato
                                   ) as ultimo_periodo_contrato
                                on ultimo_periodo_contrato.cod_contrato = registro_evento_periodo.cod_contrato
                               and ultimo_periodo_contrato.max_cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao

                          group by registro_evento_periodo.cod_contrato
                       ) as valor_pensao
                    on valor_pensao.cod_contrato = contrato.cod_contrato


                 where contrato_pensionista.dt_inicio_beneficio <= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
                   and contrato_pensionista.dt_encerramento >= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
                    or contrato_pensionista.dt_encerramento is null
    ";

    return $stSql;
}

function recuperaPensionistasDefinivel(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem) ? " ORDER BY ".$stOrdem: " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaPensionistasDefinivel().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPensionistasDefinivel()
{
    $stSql .= "   SELECT pensionista.cod_pensionista                                                                                                                                                                                                                                                                                                                                             \n";
    $stSql .= "        , sw_cgm.nom_cgm as nome                                                                                                                                                                                                                                                                                                                                            \n";
    $stSql .= "        , sw_cgm.logradouro||','||sw_cgm.numero||' - '||sw_cgm.complemento as endereço                                                                                                                                                                                                                                                                                      \n";
    $stSql .= "        , sw_cgm.bairro                                                                                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "        , sw_cgm.cep                                                                                                                                                                                                                                                                                                                                                        \n";
    $stSql .= "        , (SELECT nom_municipio FROM sw_municipio WHERE sw_municipio.cod_municipio = sw_cgm.cod_municipio AND sw_municipio.cod_uf = sw_cgm.cod_uf) as Município                                                                                                                                                                                                             \n";
    $stSql .= "        , (SELECT sigla_uf FROM sw_uf WHERE sw_uf.cod_uf = sw_cgm.cod_uf) as uf                                                                                                                                                                                                                                                                                             \n";
    $stSql .= "        , sw_cgm.fone_residencial as fone                                                                                                                                                                                                                                                                                                                                   \n";
    $stSql .= "        , (SELECT descricao FROM sw_escolaridade WHERE cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade) as escolaridade                                                                                                                                                                                                                                            \n";
    $stSql .= "        , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as Data_Nascimento                                                                                                                                                                                                                                                                                    \n";
    $stSql .= "        , sw_cgm_pessoa_fisica.cpf\n";
    $stSql .= "        , sw_cgm_pessoa_fisica.rg\n";
    $stSql .= "        , sw_processo.cod_processo as Processo\n";
    $stSql .= "        , to_char( sw_processo.timestamp, 'dd/mm/yyyy' ) as Data_Inclusão_Processo\n";
    $stSql .= "        , (SELECT registro FROM pessoal.contrato WHERE cod_contrato = contrato_pensionista.cod_contrato) as Matrícula\n";
    $stSql .= "        , (SELECT cod_grau || ' - ' || nom_grau FROM cse.grau_parentesco WHERE pensionista.cod_grau = cod_grau) as Grau_Parentesco                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "        , (SELECT cod_profissao || ' - ' || nom_profissao FROM cse.profissao WHERE pensionista.cod_profissao = cod_profissao) as Ocupação                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "        , to_char(contrato_pensionista.dt_inicio_beneficio,'dd/mm/yyyy') as Data_Início_Benefício                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "        , to_char(contrato_pensionista.dt_encerramento,'dd/mm/yyyy') as Data_Encerramento_Benefício                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "        , contrato_pensionista.motivo_encerramento as Motivo_Encerramento                                                                                                                                                                                                                                                                               \n";
    $stSql .= "        , contrato_pensionista.num_beneficio as Número_Benefício                                                                                                                                                                                                                                                                             \n";
    $stSql .= "        , contrato_pensionista.percentual_pagamento as Percentual_Pagamento_Pensão                                                                                                                                                                                                                                                                               \n";
    //$stSql .= "        , contrato_pensionista.percentual_pagamento * valor_pensao.valor as Valor_Pensão                                                                                                                                                                                                                                                                             \n";
    $stSql .= "        , (SELECT cod_dependencia || ' - ' || descricao FROM pessoal.tipo_dependencia WHERE contrato_pensionista.cod_dependencia = cod_dependencia) as Tipo_Dependência                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "        , (SELECT sigla || ' - ' || descricao FROM pessoal.cid WHERE cod_cid = pensionista_cid.cod_cid) as cid \n";
    $stSql .= "        , (SELECT num_banco FROM monetario.banco WHERE cod_banco = contrato_pensionista_conta_salario.cod_banco) ||'-'||(SELECT nom_banco FROM monetario.banco WHERE cod_banco = contrato_pensionista_conta_salario.cod_banco) as banco\n";
    $stSql .= "        , (SELECT num_agencia FROM monetario.agencia WHERE cod_agencia = contrato_pensionista_conta_salario.cod_agencia AND cod_banco = contrato_pensionista_conta_salario.cod_banco)||'-'||(SELECT nom_agencia FROM monetario.agencia WHERE cod_agencia = contrato_pensionista_conta_salario.cod_agencia AND cod_banco = contrato_pensionista_conta_salario.cod_banco) as agência      \n";
    $stSql .= "        , contrato_pensionista_conta_salario.nr_conta as conta                                                                                                                                                                                                                                                                                                                 \n";
    $stSql .= "        , recuperaDescricaoOrgao(contrato_pensionista_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotação       \n";
    $stSql .= "        , previdencia.descricao as previdência                                                                                                                                                                                                                                                                                          \n";
    $stSql .= "        , gerador_beneficio.*                                                                                                                                                                                                                                                                                          \n";
    $stSql .= $this->getDado("campo_join");
    $stSql .= "     FROM pessoal.pensionista                                                                                                                                                                                                                                                                                                                                                  \n";

    $stSql .= "        , pessoal.contrato_pensionista                                                                                                                                                                                                                                                                                                                                \n";
    $stSql .= $this->getDado("join");

    $stSql .= "LEFT JOIN (SELECT contrato_pensionista_previdencia.cod_contrato                                                 \n";
    $stSql .= "     , previdencia_previdencia.descricao                                                                     \n";
    $stSql .= "  FROM pessoal.contrato_pensionista_previdencia                                                                 \n";
    $stSql .= "     , (SELECT cod_contrato                                                                                  \n";
    $stSql .= "             , cod_previdencia                                                                               \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                   \n";
    $stSql .= "         FROM pessoal.contrato_pensionista_previdencia                                                          \n";
    $stSql .= "        GROUP BY cod_contrato                                                                                \n";
    $stSql .= "     , cod_previdencia) as max_contrato_pensionista_previdencia                                                 \n";
    $stSql .= "     , folhapagamento.previdencia_previdencia                                                                \n";
    $stSql .= "     , (SELECT cod_previdencia                                                                               \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                   \n";
    $stSql .= "         FROM folhapagamento.previdencia_previdencia                                                         \n";
    $stSql .= "        GROUP BY cod_previdencia) as max_previdencia_previdencia                                             \n";
    $stSql .= " WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato           \n";
    $stSql .= "   AND contrato_pensionista_previdencia.cod_previdencia = max_contrato_pensionista_previdencia.cod_previdencia     \n";
    $stSql .= "   AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp                 \n";
    $stSql .= "   AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia               \n";
    $stSql .= "   AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                 \n";
    $stSql .= "   AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                             \n";
    $stSql .= "   AND previdencia_previdencia.tipo_previdencia = 'o'                                                        \n";
    $stSql .= "   ) as previdencia                                    \n";
    $stSql .= "       ON contrato_pensionista.cod_contrato = previdencia.cod_contrato                                                                                                                                                                                                                                                                      \n";

    $stSql .= "LEFT JOIN (\n";
    $stSql .= "                        SELECT contrato.registro as Matricula_Gerador_Benefício\n";
    $stSql .= "                             , sw_cgm.nom_cgm as Nome_Gerador_Benefício\n";
    $stSql .= "                             , sw_cgm.numcgm as CGM_Gerador_Benefício\n";
    $stSql .= "                             , servidor_contrato_servidor.cod_contrato as cod_contrato_cedente\n";
    $stSql .= "                          FROM pessoal.contrato,\n";
    $stSql .= "                               pessoal.servidor_contrato_servidor,\n";
    $stSql .= "                               pessoal.servidor,\n";
    $stSql .= "                               sw_cgm\n";
    $stSql .= "                         WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato\n";
    $stSql .= "                           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor\n";
    $stSql .= "                           AND servidor.numcgm = sw_cgm.numcgm\n";
    $stSql .= "                   ) AS gerador_beneficio\n";
    $stSql .= "                ON gerador_beneficio.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente\n";

    $stSql .= "LEFT JOIN (\n";
    $stSql .= "                        SELECT registro_evento_periodo.cod_contrato\n";
    $stSql .= "                             , sum(registro_evento.valor) as valor\n";
    $stSql .= "                          FROM folhapagamento.registro_evento\n";
    $stSql .= "                          JOIN folhapagamento.registro_evento_periodo\n";
    $stSql .= "                            ON registro_evento_periodo.cod_registro = registro_evento.cod_registro\n";
    $stSql .= "                          JOIN (\n";
    $stSql .= "                                    select cod_contrato\n";
    $stSql .= "                                         , max( cod_periodo_movimentacao ) as max_cod_periodo_movimentacao\n";
    $stSql .= "                                      from folhapagamento.registro_evento_periodo\n";
    $stSql .= "                                  group by cod_contrato\n";
    $stSql .= "                               ) as ultimo_periodo_contrato\n";
    $stSql .= "                            ON ultimo_periodo_contrato.cod_contrato = registro_evento_periodo.cod_contrato\n";
    $stSql .= "                           AND ultimo_periodo_contrato.max_cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao\n";
    $stSql .= "                      GROUP BY registro_evento_periodo.cod_contrato\n";
    $stSql .= "                   ) AS valor_pensao\n";
    $stSql .= "                ON valor_pensao.cod_contrato = contrato_pensionista.cod_contrato\n";

    $stSql .= "LEFT JOIN pessoal.contrato_pensionista_processo                                                                                                                                                                                                                                                                                                                                 \n";
    $stSql .= "       ON contrato_pensionista.cod_contrato = contrato_pensionista_processo.cod_contrato   \n";

    $stSql .= "LEFT JOIN pessoal.pensionista_cid\n";
    $stSql .= "       ON pensionista_cid.cod_pensionista = contrato_pensionista.cod_pensionista   \n";
    $stSql .= "      AND pensionista_cid.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente   \n";

    $stSql .= "LEFT JOIN (SELECT sw_processo.*                                                                                                                                                                                                                                                                                                                                 \n";
    $stSql .= "             FROM sw_processo                                                                                                                                                                                                                                                                                                                           \n";
    $stSql .= "                , (  SELECT cod_processo                                                                                                                                                                                                                                                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "                       FROM sw_processo                                                                                                                                                                                                                                                                                                                 \n";
    $stSql .= "                   GROUP BY cod_processo) as max_processo_pensionista                                                                                                                                                                                                                                                                                                    \n";
    $stSql .= "            WHERE sw_processo.cod_processo = max_processo_pensionista.cod_processo                                                                                                                                                                                                                                                                           \n";
    $stSql .= "              AND sw_processo.timestamp = max_processo_pensionista.timestamp) as sw_processo                                                                                                                                                                                                                                                     \n";
    $stSql .= "       ON contrato_pensionista_processo.cod_processo = sw_processo.cod_processo                                                                                                                                                                                                                                                                                             \n";

    $stSql .= "LEFT JOIN (SELECT contrato_pensionista_orgao.*                                                                                                                                                                                                                                                                                                                                 \n";
    $stSql .= "             FROM pessoal.contrato_pensionista_orgao                                                                                                                                                                                                                                                                                                                           \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                                                                                                                                                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_pensionista_orgao                                                                                                                                                                                                                                                                                                                 \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                                                                                                                                                                                                                                                    \n";
    $stSql .= "            WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                                                                                                                                                                                                                                           \n";
    $stSql .= "              AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao                                                                                                                                                                                                                                                     \n";
    $stSql .= "       ON contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                                                                                                                                                                                                                                                             \n";

    $stSql .= "LEFT JOIN (SELECT contrato_pensionista_conta_salario.*\n";
    $stSql .= "             FROM pessoal.contrato_pensionista_conta_salario\n";
    $stSql .= "                 , (SELECT cod_contrato                                                                                                                                                                                                                                                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_pensionista_conta_salario                                                                                                                                                                                                                                                                                         \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_pensionista_conta_salario                                                                                                                                                                                                                                                                                                    \n";
    $stSql .= "            WHERE contrato_pensionista_conta_salario.cod_contrato = max_contrato_pensionista_conta_salario.cod_contrato                                                                                                                                                                                                                                                                           \n";
    $stSql .= "              AND contrato_pensionista_conta_salario.timestamp = max_contrato_pensionista_conta_salario.timestamp) as contrato_pensionista_conta_salario                                                                                                                                                                                                                                                     \n";
    $stSql .= "       ON contrato_pensionista.cod_contrato = contrato_pensionista_conta_salario.cod_contrato                                                                                                                                                                                                                                                                                             \n";
    $stSql .= "        , sw_cgm                                                                                                                                                                                                                                                                                                                                                            \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                                                                                                                                                                                                                                              \n";
    $stSql .= "    WHERE pensionista.numcgm = sw_cgm.numcgm                                                                                                                                                                                                                                                                                                                                   \n";
    $stSql .= "      AND pensionista.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                                                                                                                                                                                                                                     \n";
    $stSql .= "      AND pensionista.cod_pensionista = contrato_pensionista.cod_pensionista                                                                                                                                                                                                                                                                                                   \n";
    $stSql .= "      AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente   \n";

    return $stSql;
}

function recuperaPensionistaRemessaBanPara(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    if (trim($stOrdem)=="") {$stOrdem="ORDER BY nom_cgm";}
    $obErro = $this->executaRecupera("montaRecuperaPensionistaRemessaBanPara",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaPensionistaRemessaBanPara()
{
    $stSql .= "      SELECT * FROM (\n";
    $stSql .= "             SELECT UPPER(sw_cgm.nom_cgm) as nom_cgm\n";
    $stSql .= "                  , sw_cgm.numcgm\n";
    $stSql .= "                  , UPPER(sw_cgm.bairro) as bairro\n";
    $stSql .= "                  , sw_cgm.cep\n";
    $stSql .= "                  , sw_cgm.cod_municipio\n";
    $stSql .= "                  , sw_cgm.cod_pais\n";
    $stSql .= "                  , sw_cgm.cod_uf\n";
    $stSql .= "                  , UPPER(sw_cgm.complemento) as complemento\n";
    $stSql .= "                  , sw_cgm.dt_cadastro\n";
    $stSql .= "                  , sw_cgm.fone_residencial\n";
    $stSql .= "                  , UPPER(sw_cgm.logradouro) as logradouro\n";
    $stSql .= "                  , UPPER(sw_cgm.numero) as numero\n";
    $stSql .= "                  , sw_cgm_pessoa_fisica.cod_escolaridade\n";
    $stSql .= "                  , sw_cgm_pessoa_fisica.cod_uf_orgao_emissor\n";
    $stSql .= "                  , sw_cgm_pessoa_fisica.cpf\n";
    $stSql .= "                  , sw_cgm_pessoa_fisica.dt_nascimento\n";
    $stSql .= "                  , UPPER(sw_cgm_pessoa_fisica.orgao_emissor) as orgao_emissor\n";
    $stSql .= "                  , sw_cgm_pessoa_fisica.rg\n";
    $stSql .= "                  , UPPER(sw_cgm_pessoa_fisica.sexo) as sexo\n";
    $stSql .= "                  , ( SELECT UPPER(nom_municipio) as nom_municipio FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio and cod_uf = sw_cgm.cod_uf ) as cidade\n";
    $stSql .= "                  , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm.cod_uf and cod_pais = sw_cgm.cod_pais ) as uf\n";
    $stSql .= "                  , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm_pessoa_fisica.cod_uf_orgao_emissor and cod_pais = sw_cgm.cod_pais ) as uf_orgao_emissor\n";
    $stSql .= "                  , contratos_pensionista.cod_contrato\n";
    $stSql .= "                  , contratos_pensionista.registro\n";
    $stSql .= "                  , contratos_pensionista.nr_conta\n";
    $stSql .= "                  , contratos_pensionista.cod_agencia\n";
    $stSql .= "                  , contratos_pensionista.cod_banco\n";
    $stSql .= "                  , contratos_pensionista.num_banco\n";
    $stSql .= "                  , contratos_pensionista.num_agencia\n";
    $stSql .= "                  , contratos_pensionista.cod_orgao\n";
    $stSql .= "                  , contratos_pensionista.cod_local\n";
    $stSql .= "               FROM (\n";
    $stSql .= "                       SELECT cod_contrato\n";
    $stSql .= "                            , numcgm\n";
    $stSql .= "                            , nom_cgm \n";
    $stSql .= "                            , registro \n";
    $stSql .= "                            , nr_conta_salario as nr_conta \n";
    $stSql .= "                            , num_banco_salario as num_banco \n";
    $stSql .= "                            , cod_banco_salario as cod_banco\n";
    $stSql .= "                            , num_agencia_salario as num_agencia\n";
    $stSql .= "                            , cod_agencia_salario as cod_agencia\n";
    $stSql .= "                            , cod_orgao \n";
    $stSql .= "                            , cod_local \n";
    $stSql .= "                     FROM recuperarContratoPensionista('cgm,cs,o,l','".Sessao::getEntidade()."',".($this->getDado('inCodPeriodoMovimentacao')?$this->getDado('inCodPeriodoMovimentacao'):0).",'geral','','".Sessao::getExercicio()."')\n";
    $stSql .= "                    ) as contratos_pensionista\n";
    $stSql .= "         INNER JOIN sw_cgm \n";
    $stSql .= "                 ON contratos_pensionista.numcgm = sw_cgm.numcgm\n";
    $stSql .= "         INNER JOIN sw_cgm_pessoa_fisica \n";
    $stSql .= "                 ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm\n";
    $stSql .= "                  ) AS contrato\n";

    return $stSql;
}

}
