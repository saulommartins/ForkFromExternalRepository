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
  * Classe de mapeamento da tabela PESSOAL.MOV_SEFIP_SAIDA
  * Data de Criação: 04/05/2005

  * @author Analista: Leandro OLiveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

    Caso de uso: uc-04.04.07
                 uc-04.04.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.MOV_SEFIP_SAIDA
  * Data de Criação: 04/05/2005

  * @author Analista: Leandro OLiveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalMovSefipSaida extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPessoalMovSefipSaida()
    {
        parent::Persistente();
        $this->setTabela('pessoal.mov_sefip_saida');

        $this->setCampoCod('cod_sefip_saida');
        $this->setComplementoChave('');

        $this->AddCampo('cod_sefip_saida','integer',true,'',true,"TPessoalSefip","cod_sefip");
    }

    /*
        Método        : recuperaSefipSaidaSemRetorno
        Descrição     : preenche um recordSet com Sefip's de saida que não tem retorno
        Autor         : Bruce Cruz de Sena
        Data          : 07/03/2006
        Data Alteração:
        Parametros    :  &$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = ""
        Retorno       : obErro

    */

    public function recuperaSefipSaidaSemRetorno(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaSefipSaidaSemRetorno().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSefipSaidaSemRetorno()
    {
       $stSql  = "select s.cod_sefip                                                                                                      \n";
       $stSql .= "     , trim(s.descricao) as descricao                                                                                   \n";
       $stSql .= "     , trim(s.num_sefip) as num_sefip                                                                                   \n";
       $stSql .= "     , s.repetir_mensal                                                                                                 \n";
       $stSql .= "from    pessoal.mov_sefip_saida as mss                                                                                  \n";
       $stSql .= "inner join                                                                                                              \n";
       $stSql .= "        pessoal.sefip as s                                                                                              \n";
       $stSql .= "            on (mss.cod_sefip_saida = s.cod_sefip)                                                                      \n";
       $stSql .= "and not (mss.cod_sefip_saida in (select ret.cod_sefip_saida from  pessoal.mov_sefip_saida_mov_sefip_retorno as ret ))   \n";

       return $stSql;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = " SELECT      sefip.*                                                              \n";
        $stSQL .= "           , pessoal.mov_sefip_saida.cod_sefip_saida                              \n";
        $stSQL .= "           , psr.cod_sefip_retorno                                                \n";
        $stSQL .= "                                                                                  \n";
        $stSQL .= "   FROM       pessoal.mov_sefip_saida                                             \n";
        $stSQL .= "   inner join pessoal.sefip                                                       \n";
        $stSQL .= "       on (  pessoal.mov_sefip_saida.cod_sefip_saida = pessoal.sefip.cod_sefip )  \n";
        $stSQL .= "   left  join pessoal.mov_sefip_saida_mov_sefip_retorno as psr                    \n";
        $stSQL .= "       on ( psr.cod_sefip_saida = pessoal.mov_sefip_saida.cod_sefip_saida)        \n";

        return  $stSQL;
    }


    /**
        Valida exclusão de registros na tabela mov_sefip_saida, verifica ligações com a tabela assentamento_miov
        @ access public
        @return obErro
    */

    public function validaExclusao($stFiltro = "" , $boTransacao = "")
    {
        $obErro     = new Erro;
        $obConexao  = new Conexao;
        $rsConsulta = new RecordSet;

        $stSql .= $this->montaValidaExclusao();
        $stSql .= $this->getDado('cod_sefip_saida');

        $obErro  = $obConexao->executaSQL( $rsConsulta, $stSql, $boTransacao );

        if ( !$obErro->ocorreu()) {
            if ( $rsConsulta->getNumLinhas() > 0 ) {
                $obErro->setDescricao ('Esta Sefip está sendo utilizada por uma Sefip de Afastamento, por esse motivo não pode ser excluida!');
            }
        }

        return $obErro;
    }

    public function montaValidaExclusao()
    {
       $stSql  = " select  mss.*                                                                                                    \n";
       $stSql .= " from  pessoal.mov_sefip_saida as mss                                                                             \n";
       $stSql .= " where ( (mss.cod_sefip_saida in ( select ams.cod_sefip_saida from pessoal.assentamento_mov_sefip_saida as ams )) \n";
       $stSql .= "  or (mss.cod_sefip_saida in ( select  ca.cod_sefip_saida from pessoal.causa_rescisao as ca )               ))    \n";
       $stSql .= "  and mss.cod_sefip_saida = ";

        return $stSql;
    }

    public function recuperaSefipCategoria(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaSefipSaidaCategoria ().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSefipSaidaCategoria()
    {
        $stSql  = " select pmsc.*                                          \n ";
        $stSql .= "      ,pc.descricao                                     \n ";
        $stSql .= "from pessoal.mov_sefip_saida_categoria as pmsc          \n ";
        $stSql .= "inner join pessoal.categoria  as pc                     \n ";
        $stSql .= "        on (pmsc.cod_categoria  = pc.cod_categoria)     \n ";

        return $stSql;
    }

    }
