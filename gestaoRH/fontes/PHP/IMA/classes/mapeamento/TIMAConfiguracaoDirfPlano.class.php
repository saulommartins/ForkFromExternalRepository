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
    * Classe de mapeamento da tabela ima.configuracao_dirf
    * Data de Criação: 26/01/2011

    * @author Desenvolvedor: Tonismar R. Bernardo

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TIMAConfiguracaoDirfPlano extends Persistente
{
   public function TIMAConfiguracaoDirfPlano()
   {
       parent::Persistente();
       $this->setTabela("ima.configuracao_dirf_plano");

       $this->setCampoCod('');
       $this->setComplementoChave('exercicio');

       $this->AddCampo('exercicio','char',true,'4',true,true);
       $this->AddCampo('numcgm','char',true,'4',true,true);
       $this->AddCampo('registro_ans',numeric,true,'6',true,true);
       $this->AddCampo('cod_evento',integer,true,'',false,true);
   }

   public function montaRecuperaRelacionamento()
   {
        $stSql = "  SELECT
                         configuracao_dirf_plano.*
                        ,sw_cgm_pessoa_juridica.numcgm
                        ,sw_cgm_pessoa_juridica.cnpj
                        ,sw_cgm.nom_cgm
                        ,evento.codigo
                        ,evento.descricao
                      FROM
                        ima.configuracao_dirf_plano
                INNER JOIN
                        sw_cgm_pessoa_juridica
                        ON
                        configuracao_dirf_plano.numcgm = sw_cgm_pessoa_juridica.numcgm
                INNER JOIN
                        sw_cgm
                        ON
                        sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                INNER JOIN
                        folhapagamento.evento
                        ON
                        configuracao_dirf_plano.cod_evento = evento.cod_evento";

        return $stSql;
   }

   public function recuperaPlanoSaudeDirf(&$rsRecordSet,$stFiltro='',$stOrder='',$boTransacao='')
   {
       $stOrder = $stOrder.' GROUP BY registro, cod_contrato, nom_cgm, numcgm, cpf ';

       return $this->executaRecupera("montaRecuperaPlanoSaude",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
   }

   public function montaRecuperaPlanoSaude()
   {
       $stSql = " SELECT DISTINCT
                       registro
                      ,cod_contrato
                      ,nom_cgm
                      ,numcgm
                      ,cpf
                      ,sum(valor) as valor
                   FROM
                      dirfplanosaude('".Sessao::getEntidade()."','".$this->getDado('stTipoFiltro')."','".$this->getDado('stValoresFiltro')."','".$this->getDado('inExercicio')."',".$this->getDado('inCodEvento').",'') ";

       return $stSql;
   }

   public function recuperaPlanoSaudeDirfPagamento(&$rsRecordSet,$stFiltro='',$stOrder='',$boTransacao='')
   {
       $stOrder = $stOrder.' GROUP BY registro, cod_contrato, nom_cgm, numcgm, cpf ';

       return $this->executaRecupera("montaRecuperaPlanoSaudePagamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
   }

   public function montaRecuperaPlanoSaudePagamento()
   {
       $stSql = " SELECT DISTINCT
                       registro
                      ,cod_contrato
                      ,nom_cgm
                      ,numcgm
                      ,cpf
                      ,sum(valor) as valor
                   FROM
                      dirfplanosaude('".Sessao::getEntidade()."','".$this->getDado('stTipoFiltro')."','".$this->getDado('stValoresFiltro')."','".$this->getDado('inExercicio')."',".$this->getDado('inCodEvento').",'".$this->getDado('stExercicioAnterior')."') ";

       return $stSql;
   }

}
?>
