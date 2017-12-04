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
    * Classe de mapeamento da tabela ARRECADACAO.SUSPENSAO e ARRECADACAO.LANCAMENTO (para fins de trabalhar com a suspensao)
    * Data de Criação: 01/11/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Márson Luís Oliveira de Paula
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRSuspensao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.08
*/

/*
$Log$
Revision 1.17  2006/11/24 16:12:34  marson
Adição do caso de uso de Suspensão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.SUSPENSAO e ARRECADACAO.LANCAMENTO
  * Data de Criação: 01/11/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Márson Luís Oliveira de Paula

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRSuspensao extends Persistente
{
/**
    * Método Construtor Suspensao
    * @access Private
*/
    public function TARRSuspensao()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.suspensao');

        $this->setCampoCod('cod_suspensao');
        $this->setComplementoChave('cod_lancamento');

        $this->AddCampo('cod_suspensao'     ,'integer',true,'',true ,false);
        $this->AddCampo('cod_tipo_suspensao','integer',true,'',false,true );
        $this->AddCampo('inicio'            ,'date'   ,true,'',false,false);
        $this->AddCampo('observacoes'       ,'text'   ,true,'',false,false);
        $this->AddCampo('cod_lancamento'    ,'integer',true,'',true ,true );
    }

    public function montaRecuperaRelacionamento()
    {
      $stSql .="    select DISTINCT\n";
      $stSql .="           alc.cod_lancamento\n";
      $stSql .="         , (case\n";
      $stSql .="              when ic.cod_calculo  is not null then\n";
      $stSql .="                 ic.inscricao_municipal\n";
      $stSql .="              when cec.cod_calculo is not null then\n";
      $stSql .="                 cec.inscricao_economica\n";
      $stSql .="           end) as inscricao\n";
      $stSql .="         , ( arrecadacao.buscaCgmLancamento (alc.cod_lancamento)||' - '|| arrecadacao.buscaContribuinteLancamento(alc.cod_lancamento) )::varchar as proprietarios\n";
      $stSql .="         , (case\n";
      $stSql .="              when ic.cod_calculo  is not null then\n";
      $stSql .="                arrecadacao.fn_consulta_endereco_imovel(ic.inscricao_municipal)\n";
      $stSql .="              when cec.cod_calculo is not null then\n";
      $stSql .="                arrecadacao.fn_consulta_endereco_empresa(cec.inscricao_economica)\n";
      $stSql .="              else 'Nao Encontrado'\n";
      $stSql .="           end) as dados_complementares\n";
      $stSql .="         , (case\n";
      $stSql .="              when acgc.cod_grupo is not null then \n";
      $stSql .="                 acgc.cod_grupo || '/' || acgc.ano_exercicio || ' - ' || agc.descricao\n";
      $stSql .="              else\n";
      $stSql .="                 ac.cod_credito || '.' || ac.cod_especie || '.' || ac.cod_genero  || '.' || ac.cod_natureza || ' - ' || mc.descricao_credito\n";
      $stSql .="           end) as origemcobranca\n";
      $stSql .="         , suspensao.cod_suspensao\n";
      $stSql .="         , suspensao.tipo_suspensao\n";
      $stSql .="         , TO_CHAR( suspensao.inicio,'DD/MM/YYYY' ) as inicio\n";
      $stSql .="         , acgc.cod_grupo\n";
      $stSql .="      FROM arrecadacao.calculo_cgm cgm\n";
      $stSql .="INNER JOIN arrecadacao.lancamento_calculo as alc \n";
      $stSql .="        ON cgm.cod_calculo = alc.cod_calculo\n";
      $stSql .="INNER JOIN arrecadacao.lancamento as al\n";
      $stSql .="        ON al.cod_lancamento = alc.cod_lancamento\n";
      $stSql .=" LEFT JOIN arrecadacao.imovel_calculo ic\n";
      $stSql .="        ON ic.cod_calculo = alc.cod_calculo\n";
      $stSql .=" LEFT JOIN arrecadacao.cadastro_economico_calculo cec  \n";
      $stSql .="        ON cec.cod_calculo  = alc.cod_calculo\n";
      $stSql .="inner join arrecadacao.calculo_grupo_credito as acgc\n";
      $stSql .="        on alc.cod_calculo = acgc.cod_calculo\n";
      $stSql .=" left join arrecadacao.calculo as ac\n";
      $stSql .="        on alc.cod_calculo = ac.cod_calculo\n";
      $stSql .="inner join arrecadacao.grupo_credito as agc\n";
      $stSql .="        on acgc.cod_grupo = agc.cod_grupo\n";
      $stSql .=" left join monetario.credito as mc\n";
      $stSql .="        on ac.cod_credito = mc.cod_credito\n";
      $stSql .="inner join (\n";
      $stSql .="           select arrecadacao.suspensao.cod_suspensao\n";
      $stSql .="                , arrecadacao.suspensao.cod_lancamento\n";
      $stSql .="                , arrecadacao.suspensao.cod_tipo_suspensao\n";
      $stSql .="                , arrecadacao.suspensao.cod_tipo_suspensao || ' - ' || arrecadacao.tipo_suspensao.descricao as tipo_suspensao\n";
      $stSql .="                , arrecadacao.suspensao.inicio\n";
      $stSql .="             from arrecadacao.suspensao\n";
      $stSql .="       inner join arrecadacao.tipo_suspensao\n";
      $stSql .="               on arrecadacao.suspensao.cod_tipo_suspensao = arrecadacao.tipo_suspensao.cod_tipo_suspensao\n";
      $stSql .="           ) as suspensao\n";
      $stSql .="        on al.cod_lancamento = suspensao.cod_lancamento\n";

        return $stSql;
    }

    public function selecionaSuspensaoLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaSelecionaSuspensaoLancamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaSelecionaSuspensaoLancamento()
    {
        $stSql .="    select DISTINCT\n";
        $stSql .="           alc.cod_lancamento\n";
        $stSql .="         , (case\n";
        $stSql .="               when ic.cod_calculo  is not null then\n";
        $stSql .="                  ic.inscricao_municipal\n";
        $stSql .="               when cec.cod_calculo is not null then\n";
        $stSql .="                  cec.inscricao_economica\n";
        $stSql .="           end) as inscricao\n";
        $stSql .="         , ( arrecadacao.buscaCgmLancamento (alc.cod_lancamento)||' - '|| arrecadacao.buscaContribuinteLancamento(alc.cod_lancamento) )::varchar as proprietarios\n";
        $stSql .="         , (case\n";
        $stSql .="               when ic.cod_calculo  is not null then\n";
        $stSql .="                  arrecadacao.fn_consulta_endereco_imovel(ic.inscricao_municipal)\n";
        $stSql .="               when cec.cod_calculo is not null then\n";
        $stSql .="                  arrecadacao.fn_consulta_endereco_empresa(cec.inscricao_economica)\n";
        $stSql .="               else 'Nao Encontrado'\n";
        $stSql .="           end) as dados_complementares\n";
        $stSql .="         , (case\n";
        $stSql .="               when acgc.cod_grupo is not null then \n";
        $stSql .="                  acgc.cod_grupo || '/' || acgc.ano_exercicio || ' - ' || agc.descricao\n";
        $stSql .="               else\n";
        $stSql .="                  ac.cod_credito || '.' || ac.cod_especie || '.' || ac.cod_genero  || '.' || ac.cod_natureza || ' - ' || mc.descricao_credito\n";
        $stSql .="           end) as origemcobranca\n";
        $stSql .="         , acgc.cod_grupo\n";
        $stSql .="      FROM arrecadacao.calculo_cgm cgm\n";
        $stSql .="INNER JOIN arrecadacao.lancamento_calculo as alc \n";
        $stSql .="        ON cgm.cod_calculo = alc.cod_calculo\n";
        $stSql .="INNER JOIN arrecadacao.lancamento as al\n";
        $stSql .="        ON al.cod_lancamento = alc.cod_lancamento\n";
        $stSql .=" LEFT JOIN arrecadacao.imovel_calculo ic\n";
        $stSql .="        ON ic.cod_calculo = alc.cod_calculo\n";
        $stSql .=" LEFT JOIN arrecadacao.cadastro_economico_calculo cec  \n";
        $stSql .="        ON cec.cod_calculo  = alc.cod_calculo\n";
        $stSql .="inner join arrecadacao.calculo_grupo_credito as acgc\n";
        $stSql .="        on alc.cod_calculo = acgc.cod_calculo\n";
        $stSql .="inner join arrecadacao.calculo as ac\n";
        $stSql .="        on alc.cod_calculo = ac.cod_calculo\n";
        $stSql .="inner join arrecadacao.grupo_credito as agc\n";
        $stSql .="        on acgc.cod_grupo = agc.cod_grupo\n";
        $stSql .=" left join monetario.credito as mc\n";
        $stSql .="        on ac.cod_credito = mc.cod_credito\n";

        return $stSql;
    }

}
?>
