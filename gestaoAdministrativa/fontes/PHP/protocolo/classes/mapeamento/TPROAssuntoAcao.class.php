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
* Classe de Mapeamento para a tabela sw_assunto_atributo
* Data de Criação: 06/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15582 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:38:09 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.06.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPROAssuntoAcao extends Persistente
{
function TPROAssuntoAcao()
{
    parent::Persistente();
    $this->setTabela('protocolo.assunto_acao');
    $this->setComplementoChave('cod_classificacao,cod_assunto,cod_acao');

    $this->AddCampo('cod_classificacao','integer',true,	'',true,'TPROAssunto');
    $this->AddCampo('cod_assunto',		'integer',true,	'',true,'TPROAssunto');
    $this->AddCampo('cod_acao',			'integer',true,	'',true,true);
}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                                  \n";
    $stSQL .= "     gestao.nom_gestao,                                                  \n";
    $stSQL .= "     modulo.nom_modulo,                                                  \n";
    $stSQL .= "     funcionalidade.nom_funcionalidade,                                  \n";
    $stSQL .= "     acao.cod_acao,                                                      \n";
    $stSQL .= "     acao.nom_acao,                                                      \n";
    $stSQL .= "     acao.parametro                                                      \n";
    $stSQL .= " FROM                                                                    \n";
    $stSQL .= "     administracao.acao              AS acao,                            \n";
    $stSQL .= "     administracao.funcionalidade    AS funcionalidade,                  \n";
    $stSQL .= "     administracao.modulo            AS modulo,                          \n";
    $stSQL .= "     administracao.gestao            AS gestao,                          \n";
    $stSQL .= "     protocolo.assunto_acao          AS assunto_acao                     \n";
    $stSQL .= " WHERE                                                                   \n";
    $stSQL .= "     assunto_acao.cod_acao       = acao.cod_acao                     AND \n";
    $stSQL .= "     acao.cod_funcionalidade     = funcionalidade.cod_funcionalidade AND \n";
    $stSQL .= "     funcionalidade.cod_modulo   = modulo.cod_modulo                 AND \n";
    $stSQL .= "     modulo.cod_gestao           = gestao.cod_gestao                     \n";

    return $stSQL;
}

function recuperaRelacionamentoComPermissao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoComPermissao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoComPermissao()
{
    $stSQL  = " SELECT                                                                  \n";
    $stSQL .= "     gestao.nom_gestao,                                                  \n";
    $stSQL .= "     modulo.nom_modulo,                                                  \n";
    $stSQL .= "     funcionalidade.nom_funcionalidade,                                  \n";
    $stSQL .= "     acao.cod_acao,                                                      \n";
    $stSQL .= "     acao.nom_acao,                                                      \n";
    $stSQL .= "     acao.parametro                                                      \n";
    $stSQL .= " FROM                                                                    \n";
    $stSQL .= "     administracao.permissao         AS permissao,                       \n";
    $stSQL .= "     administracao.acao              AS acao,                            \n";
    $stSQL .= "     administracao.funcionalidade    AS funcionalidade,                  \n";
    $stSQL .= "     administracao.modulo            AS modulo,                          \n";
    $stSQL .= "     administracao.gestao            AS gestao,                          \n";
    $stSQL .= "     protocolo.assunto_acao          AS assunto_acao                     \n";
    $stSQL .= " WHERE                                                                   \n";
    $stSQL .= "     permissao.cod_acao          = acao.cod_acao                     AND \n";
    $stSQL .= "     permissao.ano_exercicio     = '".Sessao::getExercicio()."'          AND \n";
    $stSQL .= "     permissao.numcgm            = ".Sessao::read('numCgm')."               AND \n";
    $stSQL .= "     assunto_acao.cod_acao       = acao.cod_acao                     AND \n";
    $stSQL .= "     acao.cod_funcionalidade     = funcionalidade.cod_funcionalidade AND \n";
    $stSQL .= "     funcionalidade.cod_modulo   = modulo.cod_modulo                 AND \n";
    $stSQL .= "     modulo.cod_gestao           = gestao.cod_gestao                     \n";

    return $stSQL;
}
}
