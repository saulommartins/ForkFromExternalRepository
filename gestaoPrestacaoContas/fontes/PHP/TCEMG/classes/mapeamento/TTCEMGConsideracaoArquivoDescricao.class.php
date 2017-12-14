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
   /*
    * Classe de Mapeamento da tabela tcemg.consideracao_arquivo_descricao
    * Data de Criação   : 12/03/2014

    * @author Analista      Sergio Santos
    * @author Desenvolvedor Evandro Melos

    * @package URBEM
    * @subpackage

    $Id: TTCEMGConsideracaoArquivoDescricao.class.php 64671 2016-03-21 11:57:37Z jean $
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TTCEMGConsideracaoArquivoDescricao extends Persistente
{
    public function TTCEMGConsideracaoArquivoDescricao()
    {
        parent::Persistente();
        $this->setTabela("tcemg.consideracao_arquivo_descricao");
        
        $this->setCampoCod('cod_arquivo');
        $this->setComplementoChave('periodo,cod_entidade,exercicio,modulo_sicom');
        
        $this->AddCampo('cod_arquivo' ,'integer' , true,''     , true ,true);
        $this->AddCampo('periodo'     ,'integer' , true,''     ,false,false);
        $this->AddCampo('cod_entidade','integer' , true,''     ,false,false);
        $this->AddCampo('exercicio'   ,'char'    , true,''     ,false,false);
        $this->AddCampo('descricao'   ,'varchar' ,false,'"300"',false,false);
        $this->AddCampo('modulo_sicom','varchar' , true,'10'   ,true,false);
    }

    public function recuperaDescricaoArquivos(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = " ORDER BY consideracao_arquivo_descricao.cod_arquivo";
        $stSql = $this->montaRecuperaDescricaoArquivos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDescricaoArquivos()
    {
        $stSql  = "	SELECT  consideracao_arquivo.cod_arquivo 
                            ,consideracao_arquivo.nom_arquivo
                            ,consideracao_arquivo_descricao.cod_entidade
                            ,consideracao_arquivo_descricao.exercicio
                            ,consideracao_arquivo_descricao.descricao
                    FROM tcemg.consideracao_arquivo
                    JOIN tcemg.consideracao_arquivo_descricao
                        USING (cod_arquivo)
            ";
        return $stSql;
    }

    public function insereNovosArquivosPeriodo($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaInsereNovosArquivosPeriodo();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaInsereNovosArquivosPeriodo()
    {
        if ( $this->getDado('modulo_sicom') == 'mensal') {
            $stSql  = "
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (01,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (02,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (03,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (04,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (05,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (06,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (07,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (08,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (09,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (10,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (11,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (12,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (13,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (14,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (15,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (16,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (17,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (18,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (19,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (20,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (21,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (22,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (23,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (24,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (25,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (26,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (27,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (28,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (29,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (30,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (31,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (32,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (33,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (34,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (35,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (36,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (37,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (38,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (39,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (40,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (58,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (59,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
          
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (41,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            ";
        }
        if ( $this->getDado('modulo_sicom') == 'balancete') {
            $stSql = "
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES ( 1,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (41,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (42,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            ";
        }
        
        if ( $this->getDado('modulo_sicom') == 'planejamento') {
            $stSql = "
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (01,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (03,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (45,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (47,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (48,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (49,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (50,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (51,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (52,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (06,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (53,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (54,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (55,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (56,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (41,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            ";
        }
        
        if ( $this->getDado('modulo_sicom') == 'inclusao') {
            $stSql = "
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES ( 1,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (43,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (44,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (57,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (41,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').", '', '".$this->getDado('modulo_sicom')."' );
            ";
        }

        if ( $this->getDado('modulo_sicom') == 'folha' ) {
            $stSql = "
            INSERT INTO tcemg.consideracao_arquivo_descricao VALUES (60,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",".$this->getDado('exercicio').",'','".$this->getDado('modulo_sicom')."');
            ";
        }

        return $stSql;
    }
    
    public function __destruct(){}

}
?>
