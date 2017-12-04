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
    * Classe de mapeamento da tabela ADMINISTRACAO.GESTAO
    * Data de Criação: 01/11/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 3692 $
    $Name$
    $Author: sabrina $
    $Date: 2005-12-09 10:49:56 -0200 (Sex, 09 Dez 2005) $

    * Casos de uso: uc-01.03.91
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.GESTAO
  * Data de Criação: 01/11/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoGestao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoGestao()
{
    parent::Persistente();
    $this->setTabela('ADMINISTRACAO.GESTAO');

    $this->setCampoCod('cod_gestao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_gestao',    'integer', true, ''  , true,  true  );
    $this->AddCampo('nom_gestao',    'varchar', true, '40', false, false );
    $this->AddCampo('nom_diretorio', 'varchar', true, '80', false, false );
    $this->AddCampo('ordem',         'integer', true, ''  , false, false );
    $this->AddCampo('versao',        'varchar', true, '30', false, false );
}

/**
    * Listas as funções internas do sistema
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function listarGestoesPorUsuario(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaListarGestoesPorUsuario();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListarGestoesPorUsuario()
{
    $sSQL  = " SELECT                                              \n";
    $sSQL .= "     DISTINCT g.cod_gestao,                          \n";
    $sSQL .= "     g.nom_gestao,                                   \n";
    $sSQL .= "     g.nom_diretorio,                                \n";
    $sSQL .= "     g.ordem,                                        \n";
    $sSQL .= "     g.versao                                        \n";
    $sSQL .= " FROM                                                \n";
    $sSQL .= "     administracao.gestao         as g,              \n";
    $sSQL .= "     administracao.modulo         as m,              \n";
    $sSQL .= "     administracao.funcionalidade as f,              \n";
    $sSQL .= "     administracao.acao           as a,              \n";
    $sSQL .= "     administracao.permissao      as p               \n";
    $sSQL .= " WHERE                                               \n";
    $sSQL .= "     g.cod_gestao = m.cod_gestao AND                 \n";
    $sSQL .= "     m.cod_modulo = f.cod_modulo AND                 \n";
    $sSQL .= "     f.cod_funcionalidade = a.cod_funcionalidade AND \n";
    $sSQL .= "     a.cod_acao = p.cod_acao AND                     \n";
    $sSQL .= "     m.cod_modulo > 0 AND                            \n";
    $sSQL .= "     p.ano_exercicio = '".Sessao::getExercicio()."' AND    \n";
    $sSQL .= "     p.numcgm=".Sessao::read('numCgm')."                    \n";
    $sSQL .= " ORDER by                                            \n";
    $sSQL .= "     g.ordem                                         \n";

    return $sSQL;
}

}
