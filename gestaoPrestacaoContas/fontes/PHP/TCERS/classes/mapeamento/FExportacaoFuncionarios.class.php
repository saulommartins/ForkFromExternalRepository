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
    * Classe de mapeamento da tabela FN_EXPORTACAO_FUNCIONARIOS
    * Data de Criação: 20/02/2009

    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoFuncionarios extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoFuncionarios()
{
    parent::Persistente();
    $this->setTabela('tcers.recuperarCadastroFuncionarios');
    $this->AddCampo('cod_entidade'          ,'integer'                    ,false,'',false,false);
    $this->AddCampo('dt_inicial'            ,'varchar'                    ,false,'',false,false);
    $this->AddCampo('dt_final'              ,'varchar'                    ,false,'',false,false);
}

function montaRecuperaDadosExportacao()
{
  $stSql = "SELECT *, CASE WHEN situacao = '99' AND (observacoes = '' OR observacoes IS null) THEN 'Outros' ELSE observacoes END AS observacoes_modificado
         FROM tcers.recuperarCadastroFuncionarios('".$this->getDado('cod_entidade')."', '".$this->getDado('dt_inicial')."', '".$this->getDado('dt_final')."') as tabela (
              dt_inicial text
            , nome character varying(200)
            , dt_nascimento text
            , cpf character varying(11)
            , servidor_pis_pasep character(15)
            , rg  character varying(15)
            , sexo integer
            , cod_registro_funcionario integer
            , dt_admissao text
            , dt_rescisao text
            , Setor  character varying
            , cod_setor integer
            , cod_cargo integer
            , cargo character varying(100)
            , cbo integer
            , natureza_cargo text
            , qtd_dependentes_irrf integer
            , situacao varchar
            , observacoes varchar
            , cod_regime varchar
            , cod_sub_divisao varchar
            , cod_regime_previdencia varchar
            , cod_categoria integer
            , endereco varchar
            , cidade varchar
            , uf varchar
            , cep varchar(8)
            , carga_horaria numeric(5,2)
            , tipo_carga_horaria char
            , cedido_adido CHAR
            , onus_origem CHAR
            , ressarcimento CHAR
            , data_movimentacao varchar
             , cnpj_orgao_origem_destino varchar
            )";

    return $stSql;
}

function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosExportacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
