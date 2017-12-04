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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 29/09/2015

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Lisiane da Rosa Morais

    $Id $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 19/10/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAObrasFiscais extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
    }
    
    function recuperaObrasFiscais(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaObrasFiscais().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaObrasFiscais()
    {
        $stSql .= " SELECT 1 AS tipo_registro
                         , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                         , obra.nro_obra AS num_obra
                         , responsavel.documento AS nu_cpfcnpjfiscal
                         , obra_fiscal.matricula
                         , obra_fiscal.registro_profissional
                         , obra_fiscal.data_inicio
                         , obra_fiscal.data_final
                         , TO_CHAR(TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy'), 'mmyyyy') AS competencia
                      FROM tcmba.obra_fiscal
                INNER JOIN tcmba.obra
                        ON obra.cod_obra = obra_fiscal.cod_obra
                       AND obra.cod_entidade = obra_fiscal.cod_entidade
                       AND obra.exercicio = obra_fiscal.exercicio
                       AND obra.cod_tipo = obra_fiscal.cod_tipo
                INNER JOIN ( SELECT numcgm 
                                  , cnpj AS documento
                               FROM public.sw_cgm_pessoa_juridica 
                              UNION 
                             SELECT numcgm 
                                  , cpf AS documento
                               FROM public.sw_cgm_pessoa_fisica 
                            ) AS responsavel
                        ON responsavel.numcgm = obra_fiscal.numcgm
                     WHERE obra_fiscal.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                       AND obra_fiscal.cod_entidade in (".$this->getDado('entidades').")";
        return $stSql;
    }
}
