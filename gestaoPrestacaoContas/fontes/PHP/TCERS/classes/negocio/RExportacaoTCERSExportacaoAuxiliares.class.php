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
    * Classe de Exportação Arquivos Auxiliares
    * Data de Criação   : 01/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Exportador

    $Id: RExportacaoTCERSExportacaoAuxiliares.class.php 66443 2016-08-29 18:49:50Z michel $

    * Casos de uso: uc-02.08.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrograma.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoRestosPreEmpenho.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php";
include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";

class RExportacaoTCERSExportacaoAuxiliares
{
   /**
       *@var object
       *@acess private
   */
   public $obTOrgao;
   public $obTUnidade;
   public $obTFuncao;
   public $obTSubFuncao;
   public $obTPrograma;
   public $obTRestosPreEmpenho;
   public $obTPao;
   public $obRConfiguracaoConfiguracao;
   public $inExercicio      ;
   public $arArquivosSelecionados;
   public $stCnpjSetor;

   /**
       *@access public
       *@param object valor
   */
   public function setExercicio($valor) { $this->inExercicio               = $valor;}
   public function setArquivosSelecionados($valor) { $this->arArquivosSelecionados    = $valor;}
   public function setCnpjSetor($valor) { $this->stCnpjSetor               = $valor;}

   /**
    * @access Public
    * @param Object $valor
   */
   public function getExercicio() { return $this->inExercicio;}
   public function getArquivosSelecionados() { return $this->arArquivosSelecionados;}
   public function getCnpjSetor() { return $this->stCnpjSetor;}

   /**
       * Método Construtor
       * @access Private
   */
   public function RExportacaoTCERSExportacaoAuxiliares()
   {
       $this->obTOrgao             = new TOrcamentoOrgao();
       $this->obTUnidade           = new TOrcamentoUnidade();
       $this->obTFuncao            = new TOrcamentoFuncao();
       $this->obTSubFuncao         = new TOrcamentoSubfuncao();
       $this->obTPrograma          = new TOrcamentoPrograma();
       $this->obTRestosPreEmpenho  = new TEmpenhoRestosPreEmpenho();
       $this->obTPao               = new TOrcamentoProjetoAtividade();
       $this->obTContaDespesa      = new TOrcamentoContaDespesa();
       $this->obTRecurso           = new TOrcamentoRecurso();
       $this->obTPreEmpenho        = new TEmpenhoPreEmpenho();
       $this->obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao();
   }

   /**
    * Método abstrato
    * @access Public
    */
    public function geraRecordSet(&$arRecordSet , $stOrder = "")
    {
        $arFiltroRelatorio  = Sessao::read('filtroRelatorio');
        $stOrder            = " ORDER BY exercicio DESC ";
        $arRecordSet        = array('orgao'      =>new RecordSet(),
                                    'uniorcam'   =>new RecordSet(),
                                    'funcao'     =>new RecordSet(),
                                    'subfunc'    =>new RecordSet(),
                                    'programa'   =>new RecordSet(),
                                    'subprograma'=>new RecordSet(),
                                    'projativ'   =>new RecordSet(),
                                    'rubrica'    =>new RecordSet(),
                                    'recurso'    =>new RecordSet(),
                                    'credor'     =>new RecordSet());
        $stFiltro = "";

        if (in_array("ORGAO.TXT",$this->getArquivosSelecionados())) {
            $this->obTOrgao->setDado('exercicio',$this->getExercicio() );
            $obErro = $this->obTOrgao->recuperaDadosExportacao( $arRecordSet['orgao'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("UNIORCAM.TXT",$this->getArquivosSelecionados())) {
            $this->obRConfiguracaoConfiguracao->setCodModulo(2);
            $this->obRConfiguracaoConfiguracao->setExercicio( Sessao::getExercicio() );
            $this->obRConfiguracaoConfiguracao->setParametro("cnpj");
            $this->obRConfiguracaoConfiguracao->consultar();

            $arCnplSetor = explode('|', $this->getCnpjSetor());
            foreach ($arCnplSetor as $valor) {
               $this->obTUnidade->setDado('stCnpjSetor',$arCnplSetor[1]);
            }

            $this->obTUnidade->setDado('exercicio', Sessao::getExercicio());
            $this->obTUnidade->setDado('stEntidades', implode(',',$arFiltroRelatorio['arEntidadesSelecionadas']) );

            $identificador = '';
            $tamanhoEntidasSelecionadas = count($arFiltroRelatorio['arEntidadesSelecionadas']);

            if ($tamanhoEntidasSelecionadas <= 1) {
               $pegadado = pegaDado("parametro","administracao.configuracao","where parametro ilike 'cod_entidade_%' and exercicio = '".Sessao::getExercicio()."' and valor in ('".$this->obTUnidade->getDado('stEntidades')."');");

               if ($pegadado ==  'cod_entidade_prefeitura') {
                  $identificador = '1,3,4,6,7,8,9,10,11,12';
               }

               if ($pegadado == 'cod_entidade_camara') {
                  $identificador ='2';
               }

               if ($pegadado == 'cod_entidade_rpps') {
                  $identificador ='5';
               }

               if ($pegadado == 'cod_entidade_consorcio') {
                  $identificador ='11';
               }
            } else {
               $arIdentificador = array();

               foreach($arFiltroRelatorio['arEntidadesSelecionadas'] AS $inCodEntidade){
                  $pegadado = pegaDado("parametro","administracao.configuracao","where parametro ilike 'cod_entidade_%' and exercicio = '".Sessao::getExercicio()."' and valor in ('".$inCodEntidade."');");

                  if ($pegadado ==  'cod_entidade_prefeitura') {
                     for($i=1; $i<13; $i++){
                        $arIdentificador[$i] = $i;
                     }
                     break;
                  }
                  elseif ($pegadado == 'cod_entidade_camara') {
                     $arIdentificador[2] = 2;
                  }
                  elseif ($pegadado == 'cod_entidade_rpps') {
                     $arIdentificador[5] = 5;
                  }
                  elseif ($pegadado == 'cod_entidade_consorcio') {
                     $arIdentificador[11] = 11;
                  }
               }

               ksort($arIdentificador);
               $identificador = implode(',',$arIdentificador);
            }

            //verifica identificador não vazio e seta os dados
            if (!empty($identificador)) {
               $stFiltroEntidade = $identificador;
               $this->obTUnidade->setDado('identificador',$stFiltroEntidade);
            }

            $obErro = $this->obTUnidade->recuperaDadosExportacao( $arRecordSet['uniorcam'], $stFiltro, $stOrder );

            if ($obErro->ocorreu()) { return $obErro; }
        }

        if (in_array("FUNCAO.TXT",$this->getArquivosSelecionados())) {
            $this->obTFuncao->setDado( 'exercicio',$this->getExercicio() );
            $obErro = $this->obTFuncao->recuperaDadosExportacao( $arRecordSet['funcao'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("SUBFUNC.TXT",$this->getArquivosSelecionados())) {
            $this->obTSubFuncao->setDado( 'exercicio',$this->getExercicio());
            $obErro = $this->obTSubFuncao->recuperaDadosExportacao( $arRecordSet['subfunc'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("PROGRAMA.TXT",$this->getArquivosSelecionados())) {
            $this->obTPrograma->setDado( 'exercicio',$this->getExercicio());
            $obErro = $this->obTPrograma->recuperaDadosExportacao( $arRecordSet['programa'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("SUBPROG.TXT",$this->getArquivosSelecionados())) {
            $this->obTRestosPreEmpenho->setDado('exercicio', Sessao::getExercicio());
            $obErro = $this->obTRestosPreEmpenho->recuperaDadosExportacao( $arRecordSet['subprograma'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("PROJATIV.TXT",$this->getArquivosSelecionados())) {
            $this->obTPao->setDado( 'exercicio',$this->getExercicio());
            $obErro = $this->obTPao->recuperaDadosExportacao( $arRecordSet['projativ'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("RUBRICA.TXT",$this->getArquivosSelecionados())) {
            $this->obTContaDespesa->setDado( 'exercicio',$this->getExercicio());
            $obErro = $this->obTContaDespesa->recuperaDadosExportacao( $arRecordSet['rubrica'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("RECURSO.TXT",$this->getArquivosSelecionados())) {
            $this->obTRecurso->setDado( 'inExercicio',$this->getExercicio() );
            $obErro = $this->obTRecurso->recuperaDadosExportacao( $arRecordSet['recurso'], $stFiltro );
            if ($obErro->ocorreu()) { return $obErro; }
        }
        if (in_array("CREDOR.TXT",$this->getArquivosSelecionados())) {
            $this->obTPreEmpenho->setDado( 'inExercicio',$this->getExercicio() );
            $obErro = $this->obTPreEmpenho->recuperaDadosExportacaoCredor( $arRecordSet['credor'], $stFiltro, "numcgm ASC" );
            if ($obErro->ocorreu()) { return $obErro; }
        }
    }

}
