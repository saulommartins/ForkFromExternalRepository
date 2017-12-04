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
  * Página de Mapeamento da tabela: tcemg.registro_precos_orgao
  * Data de Criação: 27/02/2015

  * @author Analista:      Gelson
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGRegistroPrecosOrgao.class.php 63322 2015-08-18 13:58:14Z michel $
  $Date: 2015-08-18 10:58:14 -0300 (Tue, 18 Aug 2015) $
  $Author: michel $
  $Rev: 63322 $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGRegistroPrecosOrgao extends Persistente {
    
    /**
    * Método Construtor
    * @access Public
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcemg.registro_precos_orgao');
        $this->setComplementoChave('cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, numcgm_gerenciador, exercicio_unidade, num_unidade, num_orgao');

        $this->AddCampo('cod_entidade'                , 'integer', true,  '',  true,  true);
        $this->AddCampo('numero_registro_precos'      , 'integer', true,  '',  true,  true);
        $this->AddCampo('exercicio_registro_precos'   , 'varchar', true, '4',  true,  true);
        $this->AddCampo('interno'                     , 'boolean', true,  '',  true,  true);
        $this->AddCampo('numcgm_gerenciador'          , 'integer', true,  '',  true,  true);
        $this->AddCampo('exercicio_unidade'           , 'varchar', true, '4',  true,  true);
        $this->AddCampo('num_unidade'                 , 'integer', true,  '',  true,  true);
        $this->AddCampo('num_orgao'                   , 'integer', true,  '',  true,  true);
        $this->AddCampo('participante'                , 'boolean', true,  '', false, false);
        $this->AddCampo('numero_processo_adesao'      , 'varchar',false,'12', false, false);
        $this->AddCampo('exercicio_adesao'            , 'varchar',false, '4', false, false);
        $this->AddCampo('dt_publicacao_aviso_intencao',    'date',false,  '', false, false);
        $this->AddCampo('dt_adesao'                   ,    'date',false,  '', false, false);
        $this->AddCampo('gerenciador'                 , 'boolean', true, '4', false, false);
        $this->AddCampo('cgm_aprovacao'               , 'integer', true,  '', false,  true);
    }

    public function recuperaProcessoOrgao(&$rsRecordSet)
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaProcessoOrgao($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaProcessoOrgao()
    {
        $stSql = "
            SELECT registro_precos_orgao.cod_entidade
                 , registro_precos_orgao.numero_registro_precos
                 , registro_precos_orgao.exercicio_registro_precos
                 , registro_precos_orgao.interno
                 , registro_precos_orgao.numcgm_gerenciador
                 , registro_precos_orgao.exercicio_unidade
                 , registro_precos_orgao.num_unidade
                 , registro_precos_orgao.num_orgao
                 , registro_precos_orgao.participante
                 , registro_precos_orgao.numero_processo_adesao
                 , registro_precos_orgao.exercicio_adesao
                 , registro_precos_orgao.gerenciador
                 , registro_precos_orgao.cgm_aprovacao
                 , TO_CHAR(dt_publicacao_aviso_intencao,'dd/mm/yyyy') AS dt_publicacao_aviso_intencao
                 , TO_CHAR(dt_adesao,'dd/mm/yyyy') AS dt_adesao
                 , sw_cgm.numcgm  AS numcgm_responsavel
                 , sw_cgm.nom_cgm AS nomcgm_responsavel
                 , sw_cgm.numcgm||' - '||sw_cgm.nom_cgm AS st_cgm_responsavel
              FROM tcemg.registro_precos_orgao

         LEFT JOIN sw_cgm
                ON sw_cgm.numcgm = registro_precos_orgao.cgm_aprovacao
               AND registro_precos_orgao.cgm_aprovacao NOT IN (0)

             WHERE registro_precos_orgao.exercicio_registro_precos	= '".$this->getDado('exercicio_registro_precos')."'
               AND registro_precos_orgao.numero_registro_precos 	= ".$this->getDado('numero_registro_precos')."
               AND registro_precos_orgao.interno                	= ".$this->getDado('interno')."
               AND registro_precos_orgao.numcgm_gerenciador     	= ".$this->getDado('numcgm_gerenciador')."
               AND registro_precos_orgao.cod_entidade           	= ".$this->getDado('cod_entidade') ;

        return $stSql;
    }

    public function __destruct(){}
    
}

?>