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
    * Classe de mapeamento da tabela ima.configuracao_rais
    * Data de Criação: 25/10/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TIMAConfiguracaoRais.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.04.12
*/
/*
$Log: base.php,v $
Revision 1.3  2007/07/25 13:47:01  souzadl
alterado

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.configuracao_rais
  * Data de Criação: 25/10/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConfiguracaoRais extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConfiguracaoRais()
{
    parent::Persistente();
    $this->setTabela("ima.configuracao_rais");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio'                 ,'varchar',true  ,'4'  ,true,false);
    $this->AddCampo('numcgm'                    ,'integer',true  ,''   ,false,'TCGMCGM');
    $this->AddCampo('tipo_inscricao'            ,'varchar',true  ,'1'  ,false,false);
    $this->AddCampo('telefone'                  ,'varchar',true  ,'11' ,false,false);
    $this->AddCampo('email'                     ,'varchar',true  ,'30' ,false,false);
    $this->AddCampo('natureza_juridica'         ,'varchar',true  ,'4'  ,false,false);
    $this->AddCampo('cod_municipio'             ,'integer',true  ,''   ,false,false);
    $this->AddCampo('dt_base_categoria'         ,'integer',true  ,''   ,false,false);
    $this->AddCampo('cei_vinculado'             ,'boolean',true  ,''   ,false,false);
    $this->AddCampo('numero_cei'                ,'integer',false ,''   ,false,false);
    $this->AddCampo('prefixo'                   ,'integer',false ,''   ,false,false);
    $this->AddCampo('cod_tipo_controle_ponto'   ,'integer',true  ,''   ,false,false);

}

function TIMATipoControlePonto()
{
    parent::Persistente();
    $this->setTabela("ima.tipo_controle_ponto");
    $this->AddCampo('descricao'            ,'varchar',true  ,'150' ,false,false);
    $this->AddCampo('cod_tipo_controle_ponto'    ,'integer',true  ,''   ,false,false);

}

function recuperaExportarArquivoRais(&$rsRecordSet,$stFiltro="",$stOrdem="")
{
    $obErro = $this->executaRecupera("montaRecuperaExportarArquivoRais",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaExportarArquivoRais()
{
    $stSql  = "   SELECT cadastro.nom_cgm                                                                                                                                                              \n";
    $stSql .= "        , to_char(cadastro.dt_nascimento,'ddmmyyyy') as dt_nascimento                                                                                                                   \n";
    $stSql .= "        , (SELECT cod_rais FROM sw_pais WHERE sw_cgm_pessoa_fisica.cod_nacionalidade = cod_pais) as nacionalidade                                                                       \n";
    $stSql .= "        , sw_cgm_pessoa_fisica.cod_escolaridade                                                                                                                                         \n";
    $stSql .= "        , sw_cgm_pessoa_fisica.cpf                                                                                                                                                      \n";
    $stSql .= "        , sw_cgm_pessoa_fisica.sexo                                                                                                                                                     \n";
    $stSql .= "        , sw_cgm_pessoa_fisica.servidor_pis_pasep                                                                                                                                       \n";
    $stSql .= "        , ctps.numero                                                                                                                                                                   \n";
    $stSql .= "        , ctps.serie                                                                                                                                                                    \n";
    $stSql .= "        , to_char(cadastro.dt_admissao,'ddmmyyyy') as dt_admissao                                                                                                                       \n";
    $stSql .= "        , cadastro.cod_tipo_admissao                                                                                                                                                    \n";
    $stSql .= "        , cadastro.cod_tipo_salario                                                                                                                                                     \n";
    $stSql .= "        , cadastro.cod_vinculo                                                                                                                                                          \n";
    $stSql .= "        , cadastro.cod_contrato                                                                                                                                                         \n";
    $stSql .= "        , cadastro.registro                                                                                                                                                             \n";
    $stSql .= "        , cadastro.numcgm_sindicato                                                                                                                                                     \n";
    $stSql .= "        , (SELECT cnpj FROM sw_cgm_pessoa_juridica WHERE numcgm = cadastro.numcgm_sindicato) as cnpj_sindicato                                                                          \n";
    $stSql .= "        , cadastro.salario                                                                                                                                                              \n";
    $stSql .= "        , cadastro.horas_semanais                                                                                                                                                       \n";
    $stSql .= "        , (SELECT codigo FROM pessoal.cbo WHERE cod_cbo = cadastro.cod_cbo_funcao) as numero_cbo                                                                                        \n";
    $stSql .= "        , contrato_servidor_caso_causa.num_causa                                                                                                                                        \n";
    $stSql .= "        , contrato_servidor_caso_causa.dt_rescisao                                                                                                                                      \n";
    $stSql .= "        , (SELECT cod_rais FROM cse.raca WHERE cadastro.cod_raca = raca.cod_raca) as raca                                                                                               \n";
    $stSql .= "        , cadastro.cod_cid                                                                                                                                                              \n";
    $stSql .= "     FROM (\n";
    $stSql .= "            SELECT * FROM recuperarContratoServidor('anp,cid,cgm,si,l,o,ef,f,s','".Sessao::getEntidade()."',0,'".$this->getDado('stTipoFiltro')."','".$this->getDado('stCodigos')."','".$this->getDado('exercicio')."')\n";
    $stSql .= "          ) as cadastro\n";

    $stSql .= "LEFT JOIN (SELECT servidor_ctps.*                                                                                                                                                       \n";
    $stSql .= "                , ctps.numero                                                                                                                                                           \n";
    $stSql .= "                , ctps.serie                                                                                                                                                            \n";
    $stSql .= "             FROM pessoal.servidor_ctps                                                                                                                                                 \n";
    $stSql .= "                , pessoal.ctps                                                                                                                                                          \n";
    $stSql .= "                , (  SELECT cod_ctps                                                                                                                                                    \n";
    $stSql .= "                          , max(dt_emissao) as dt_emissao                                                                                                                               \n";
    $stSql .= "                       FROM pessoal.ctps                                                                                                                                                \n";
    $stSql .= "                   GROUP BY cod_ctps) as max_ctps                                                                                                                                       \n";
    $stSql .= "            WHERE servidor_ctps.cod_ctps = ctps.cod_ctps                                                                                                                                \n";
    $stSql .= "              AND ctps.cod_ctps = max_ctps.cod_ctps                                                                                                                                     \n";
    $stSql .= "              AND ctps.dt_emissao = max_ctps.dt_emissao) as ctps                                                                                                                        \n";
    $stSql .= "       ON ctps.cod_servidor = cadastro.cod_servidor                                                                                                                                     \n";

    $stSql .= "LEFT JOIN (SELECT contrato_servidor_caso_causa.cod_contrato                                                                                                                             \n";
    $stSql .= "                , to_char(contrato_servidor_caso_causa.dt_rescisao,'ddmm') as dt_rescisao                                                                                               \n";
    $stSql .= "                , causa_rescisao.num_causa                                                                                                                                              \n";
    $stSql .= "             FROM pessoal.contrato_servidor_caso_causa                                                                                                                                  \n";
    $stSql .= "                , pessoal.caso_causa                                                                                                                                                    \n";
    $stSql .= "                , pessoal.causa_rescisao                                                                                                                                                \n";
    $stSql .= "            WHERE contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa                                                                                               \n";
    $stSql .= "              AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao                                                                                                     \n";
    $stSql .= "              AND to_char(dt_rescisao,'yyyy') = '".$this->getDado("exercicio")."') as contrato_servidor_caso_causa                                                                      \n";
    $stSql .= "       ON contrato_servidor_caso_causa.cod_contrato = cadastro.cod_contrato                                                                                                             \n";
    $stSql .= "        , sw_cgm                                                                                                                                                                        \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                                                          \n";
    $stSql .= "    WHERE cadastro.numcgm = sw_cgm.numcgm                                                                                                                                               \n";
    $stSql .= "      AND cadastro.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                                                 \n";
    $stSql .= "      AND NOT EXISTS (SELECT 1                                                                                                                                                          \n";
    $stSql .= "                        FROM pessoal.contrato_servidor_caso_causa                                                                                                                       \n";
    $stSql .= "                       WHERE cadastro.cod_contrato = contrato_servidor_caso_causa.cod_contrato                                                                                 \n";
    $stSql .= "                         AND to_char(dt_rescisao,'yyyy') < '".$this->getDado("exercicio")."')                                                                                           \n";

    return $stSql;
}

function recuperaTipoControlePonto(&$rsRecordSet,$stFiltro="",$stOrdem="")
{
    if ($stOrdem==""||$stOrdem==null) {
        $stOrdem = " ORDER BY cod_tipo_controle_ponto ASC";
    }
    $obErro = $this->executaRecupera("montaRecuperaTipoControlePonto",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaTipoControlePonto()
{
    $stSql  = "SELECT * FROM ima.tipo_controle_ponto ";

    return $stSql;
}

}
?>
