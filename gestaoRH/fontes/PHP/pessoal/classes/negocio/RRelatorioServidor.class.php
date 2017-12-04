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
* Classe de regra de relatório para Vale-Transporte
* Data de Criação: 14/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30772 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
// include_once( "../../../bibliotecas/mascaras.lib.php"              );
include_once ( CLA_PERSISTENTE_RELATORIO      );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"              );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"    );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPadrao.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNivelPadrao.class.php");
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php");

class RRelatorioServidor extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRPessoalServidor;
/**
    * @var Object
    * @access Private
*/
var $obRPessoalContratoServidor;
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoPrevidencia;

/**
     * @access Public
     * @param Object $valor
*/
function setRPessoalServidor($valor) { $this->obRPessoalServidor = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRPessoalContratoServidor($valor) { $this->obRPessoalContratoServidor = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoPrevidencia($valor) { $this->obRFolhaPagamentoPrevidencia = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRPessoalServidor() { return $this->obRPessoalServidor;           }
/**
     * @access Public
     * @param Object $valor
*/
function getRPessoalContratoServidor() { return $this->obRPessoalContratoServidor;           }
/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoPrevidencia() { return $this->obRFolhaPagamentoPrevidencia;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioServidor()
{
    $this->setRPessoalServidor( new RPessoalServidor );
    $this->setRPessoalContratoServidor( new RPessoalContratoServidor( $this->obRPessoalServidor ) );
    $this->setRFolhaPagamentoPrevidencia( new RFolhaPagamentoPrevidencia );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset , $stFiltro , $stOrder)
{
    include_once(CAM_GA_CGM_MAPEAMENTO."TEstadoCivil.class.php");
    include_once(CAM_GA_CGM_MAPEAMENTO."TRaca.class.php");
    
    $this->obRPessoalServidor->listarRelatorio( $rsRecordSet, $stFiltro , $stOrder ,$boTransacao);
    
    //Filtros da sessão
    $arFiltro  = Sessao::read('filtroRelatorio');
    $arContratos = Sessao::read('arContratos');

    while ( !$rsRecordSet->eof() ) {
        //UNSETS para zerar parametros e filtros das classes de regra para cada registro
        unset($this->obRPessoalServidor);
        unset($this->obRPessoalContratoServidor);
        unset($this->obRFolhaPagamentoPrevidencia);
        $this->RRelatorioServidor();
        
        switch ($arFiltro['stTipoFiltro']) {
            case 'lotacao':
                $arDadosSpans['span'] = $rsRecordSet->getCampo('cod_orgao');
                $arDadosSpans['tipo'] = 'Lotação';
                $arDadosSpans['index'] = 'Span1';
            break;
            case 'local':
                $arDadosSpans['span'] = $rsRecordSet->getCampo('filtro_local');
                $arDadosSpans['tipo'] = 'Local';
                $arDadosSpans['index'] = 'Span2';
            break;
        }
        
        $arDadosTitulo['matricula'] = $rsRecordSet->getCampo('registro') ." - ". $rsRecordSet->getCampo('nom_cgm');
        
        if (isset($arFiltro['boFoto']) && $arFiltro['boFoto'] == true) { 
            $stPathImg = $rsRecordSet->getCampo('caminho_foto');
            if (!empty($stPathImg)) {
                $arDadosTitulo['foto'] = CAM_GRH_PES_ANEXOS.$rsRecordSet->getCampo('caminho_foto');
            } else {
                $arDadosTitulo['foto'] = CAM_GRH_PES_ANEXOS."no_foto.jpg";
            }
        }

        if ( isset($arFiltro['boIdentificacao']) ) {    
            //DADOS DE IDENTIFICACAO
            $arDadosIdentificacao['dt_nascimento'] = $rsRecordSet->getCampo('dt_nascimento');
            if ( $rsRecordSet->getCampo('sexo') == 'm' ) {
                $arDadosIdentificacao['sexo'] = "Masculino";
            }
            elseif ( $rsRecordSet->getCampo('sexo') == 'f' ) {
                $arDadosIdentificacao['sexo'] = "Feminino";
            } else {
                $arDadosIdentificacao['sexo'] = "";
            }
            $arDadosIdentificacao['escolaridade'] = $rsRecordSet->getCampo('escolaridade');
            $arDadosIdentificacao['nome_pai']     = $rsRecordSet->getCampo('nome_pai');
            $arDadosIdentificacao['nome_mae']     = $rsRecordSet->getCampo('nome_mae');

            $rsEstadoCivil = new Recordset;
            $obTEstadoCivil = new TEstadoCivil;
            $obTEstadoCivil->setDado('cod_estado',$rsRecordSet->getCampo('cod_estado_civil'));
            $obTEstadoCivil->recuperaPorChave( $rsEstadoCivil );
            $arDadosIdentificacao['estado_civil'] = $rsEstadoCivil->getCampo('nom_estado');

            $rsRaca = new Recordset;
            $obTRaca = new TRaca;
            $obTRaca->setDado('cod_raca',$rsRecordSet->getCampo('cod_raca'));
            $obTRaca->recuperaPorChave( $rsRaca );
            $arDadosIdentificacao['raca'] = $rsRaca->getCampo('nom_raca');
            if ( $rsRecordSet->getCampo('sigla') OR $rsRecordSet->getCampo('descricao') ) {
                $stCID = $rsRecordSet->getCampo('sigla')."/".$rsRecordSet->getCampo('descricao');
            } else {
                $stCID = "";
            }
            $arDadosIdentificacao['cid']            = $stCID;
            $arDadosIdentificacao['nacionalidade']  = $rsRecordSet->getCampo('nacionalidade');
            $arDadosIdentificacao['cod_municipio']  = $rsRecordSet->getCampo('cod_municipio');
            $arDadosIdentificacao['nom_municipio']  = $rsRecordSet->getCampo('nom_municipio');
            $arDadosIdentificacao['sigla_uf']       = $rsRecordSet->getCampo('sigla_uf');
            $arDadosIdentificacao['endereco']       = $rsRecordSet->getCampo('tipo_logradouro') .", ". $rsRecordSet->getCampo('logradouro') . ", " .$rsRecordSet->getCampo('numero');
            $arDadosIdentificacao['complemento']    = $rsRecordSet->getCampo('complemento');
            $arDadosIdentificacao['bairro']         = $rsRecordSet->getCampo('bairro');
            $arDadosIdentificacao['cep']            = $rsRecordSet->getCampo('cep');
            $arDadosIdentificacao['cidade']         = $rsRecordSet->getCampo('nom_municipio')."/".$rsRecordSet->getCampo('sigla_uf');                
            $arDadosIdentificacao['fone']           = $rsRecordSet->getCampo('fone_residencial')."/".$rsRecordSet->getCampo('fone_celular');
            $arDadosIdentificacao['e_mail']         = $rsRecordSet->getCampo('e_mail');
        }
        
        //Gera Recordset com as informações de documentação do servidor
        if ( isset($arFiltro['boDocumentacao']) ) {
            
            $arDadosDocumentacao['cpf']                = $rsRecordSet->getCampo('cpf');
            $arDadosDocumentacao['rg']                 = $rsRecordSet->getCampo('rg');
            $arDadosDocumentacao['dt_emissao_rg']      = $rsRecordSet->getCampo('dt_emissao_rg');
            $arDadosDocumentacao['orgao_emissor']      = $rsRecordSet->getCampo('orgao_emissor')."/".$rsRecordSet->getCampo('sigla_uf');
            $arDadosDocumentacao['pis_pasep']          = $rsRecordSet->getCampo('servidor_pis_pasep');
            $arDadosDocumentacao['cadastro_pis_pasep'] = $rsRecordSet->getCampo('dt_pis_pasep');
            $arDadosDocumentacao['titulo_eleitor']     = $rsRecordSet->getCampo('nr_titulo_eleitor');
            $arDadosDocumentacao['zona']               = $rsRecordSet->getCampo('zona_titulo');
            $arDadosDocumentacao['secao']              = $rsRecordSet->getCampo('secao_titulo');
            $arDadosDocumentacao['num_cnh']            = $rsRecordSet->getCampo('num_cnh');
            $arDadosDocumentacao['categoria_cnh']      = $rsRecordSet->getCampo('nom_categoria');
            $arDadosDocumentacao['dt_validade_cnh']    = $rsRecordSet->getCampo('dt_validade_cnh');
                
            $rsCTPS = new Recordset;
            $this->obRPessoalServidor->setCodServidor( $rsRecordSet->getCampo('cod_servidor') );
            $this->obRPessoalServidor->addRPessoalCTPS();
            $this->obRPessoalServidor->roRPessoalCTPS->listarCTPS( $rsCTPS );
                
            $arDadosDocumentacao['num_ctps']               = $rsCTPS->getCampo('numero');
            $arDadosDocumentacao['serie_ctps']             = $rsCTPS->getCampo('serie');
            $arDadosDocumentacao['dt_emissao_ctps']        = $rsCTPS->getCampo('dt_emissao');
            $arDadosDocumentacao['orgao_emissao_ctps']     = $rsCTPS->getCampo('orgao_expedidor')."/".$rsRecordSet->getCampo('sigla_uf');
            $arDadosDocumentacao['certificado_reservista'] = $rsRecordSet->getCampo('nr_carteira_res');
            $arDadosDocumentacao['conselho_profissional']  = $rsRecordSet->getCampo('sigla_conselho');
            $arDadosDocumentacao['num_conselho']           = $rsRecordSet->getCampo('nr_conselho');
            $arDadosDocumentacao['dt_validade_conselho']   = $rsRecordSet->getCampo('dt_validade_conselho');
                
        }
        
        //Gera Recordset com as informações contratuais do servidor        
        if ( isset($arFiltro['boContratuais']) ) {
            
            $rsContratos = new Recordset;
            $this->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->setNumCGM( $rsRecordSet->getCampo('numcgm') );
            $this->obRPessoalContratoServidor->setRegistro( $rsRecordSet->getCampo('registro') );
            $this->obRPessoalContratoServidor->listarContratosServidorRelatorio( $rsContratos );
            
            $arDadosContratuais['situacao']    = $rsContratos->getCampo('situacao');
            $arDadosContratuais['dt_nomeacao'] = $rsContratos->getCampo('dt_nomeacao');
            
            $this->obRPessoalContratoServidor->obRNorma->setCodNorma($rsContratos->getCampo('cod_norma'));
            $this->obRPessoalContratoServidor->obRNorma->listar($rsNorma);
            
            $arDadosContratuais['portaria']    = $rsNorma->getCampo('num_norma_exercicio'). " - " .$rsNorma->getCampo('nom_norma');
            $arDadosContratuais['dt_posse']    = $rsContratos->getCampo('dt_posse');
            $arDadosContratuais['dt_admissao'] = $rsContratos->getCampo('dt_admissao');
            $dt_rescisao = $rsContratos->getCampo('dt_rescisao') ? $rsContratos->getCampo('dt_rescisao') : "";
            $arDadosContratuais['dt_rescisao'] = $dt_rescisao;
            $stMotivo = $rsContratos->getCampo('motivo') ? $rsContratos->getCampo('motivo') : "";
            $arDadosContratuais['motivo'] = $stMotivo;

            //REGIME / SUBDIVISAO CARGO
            $rsRegime = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalRegime->setCodRegime( $rsContratos->getCampo('cod_regime') );
            $this->obRPessoalContratoServidor->obRPessoalRegime->listarRegime($rsRegime);
            $rsSubdivisao = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalCargo->addCargoSubDivisao();
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->listarSubDivisao( $rsSubdivisao, " AND psd.cod_sub_divisao = ".$rsContratos->getCampo('cod_sub_divisao'),$boTransacao );            
            $arDadosContratuais['regime_subdivisao_cargo'] = $rsRegime->getCampo('descricao')." / ".$rsSubdivisao->getCampo('nom_sub_divisao');

            //CARGO / ESPECIALIDADE                
            $rsCargo = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalCargo->setCodCargo( $rsContratos->getCampo('cod_cargo') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->listarCargo( $rsCargo );
            $rsEspecialidade = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->roPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $rsContratos->getCampo('cod_sub_divisao') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $rsContratos->getCampo('cod_especialidade_cargo') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->roPessoalCargo->setCodCargo ( $rsContratos->getCampo('cod_cargo') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
            $arDadosContratuais['cargo_especialidade'] = $rsCargo->getCampo('descricao')." / ".$rsEspecialidade->getCampo('descricao_especialidade');

            //REGIME / SUBDIVISAO FUNCAO
            $rsRegime = new Recordset;
            $this->obRPessoalContratoServidor->setCodContrato( $rsContratos->getCampo('cod_contrato') );
            $this->obRPessoalContratoServidor->consultarContratoServidorRegimeFuncao($rsRegimeFuncao);
            $this->obRPessoalContratoServidor->obRPessoalRegime->setCodRegime( $rsRegimeFuncao->getCampo('cod_regime') );
            $this->obRPessoalContratoServidor->obRPessoalRegime->listarRegime($rsRegime);
            $rsSubdivisao = new Recordset;
            $this->obRPessoalContratoServidor->consultarContratoServidorSubDivisaoFuncao($rsSubDivisaoFuncao);
            $this->obRPessoalContratoServidor->obRPessoalCargo->addCargoSubDivisao();            
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->listarSubDivisao( $rsSubdivisao, " AND psd.cod_sub_divisao = ".$rsSubDivisaoFuncao->getCampo('cod_sub_divisao') );            
            $arDadosContratuais['regime_subdivisao_funcao'] = $rsRegime->getCampo('descricao')." / ".$rsSubdivisao->getCampo('nom_sub_divisao');
                    
            //FUNCAO / ESPECIALIDADE
            $rsFuncao = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalCargo->setCodCargo( $rsContratos->getCampo('cod_funcao') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->listarCargo( $rsFuncao );
            $rsEspecialidade = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->roPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $rsContratos->getCampo('cod_sub_divisao_funcao') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $rsContratos->getCampo('cod_especialidade_funcao') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->roPessoalCargo->setCodCargo ( $rsContratos->getCampo('cod_funcao') );
            $this->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
            $arDadosContratuais['funcao_especialidade'] = $rsFuncao->getCampo('descricao')." / ".$rsEspecialidade->getCampo('descricao_especialidade');
                          
            $arDadosContratuais['dt_alteracao_funcao']  = $rsContratos->getCampo('dt_alteracao_funcao');

            $rsTipoAdmissao = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalTipoAdmissao->listarTipoAdmissao($rsTipoAdmissao, " WHERE cod_tipo_admissao =".$rsContratos->getCampo('cod_tipo_admissao'));
            $arDadosContratuais['tipo_admissao'] = $rsTipoAdmissao->getCampo('descricao');

            $rsVinculo = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalVinculoEmpregaticio->listarVinculoEmpregaticio($rsVinculo, " WHERE cod_vinculo =".$rsContratos->getCampo('cod_vinculo'));
            $arDadosContratuais['vinculo'] = $rsVinculo->getCampo('descricao');

            $rsCategoria = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalCategoria->listarCategoria($rsCategoria," AND cod_categoria =".$rsContratos->getCampo('cod_categoria'));
            $arDadosContratuais['categoria'] = $rsCategoria->getCampo('descricao');

            $this->obRPessoalContratoServidor->listarContratoServidorExameMedico($rsExameMedico);
            $arDadosContratuais['dt_validade_exame'] = $rsExameMedico->getCampo('dt_validade_exame');
               
        }

        //Gera Recordset com as informações salariais do servidor
        if ( isset($arFiltro['boSalariais']) ) {
                    
            $this->obRPessoalContratoServidor->setCodContrato( $rsRecordSet->getCampo('cod_contrato') );
            $this->obRPessoalContratoServidor->listarContratoServidorSalario($rsSalario);
            
            $rsSalario->addFormatacao("salario", "NUMERIC_BR");
            
            $arDadosSalario['horas_mensais']  = $rsSalario->getCampo('horas_mensais');
            $arDadosSalario['horas_semanais'] = $rsSalario->getCampo('horas_semanais');
                
            //PADRAO SOCIAL
            $rsPadrao = new Recordset;                    
            $obTPessoalContratoServidorPadrao = new TPessoalContratoServidorPadrao;
            $stFiltro  = " AND pp.cod_contrato = ".$rsRecordSet->getCampo('cod_contrato');
            $stFiltro .= " AND pp.cod_padrao   = ".$rsRecordSet->getCampo('cod_padrao');
            $obTPessoalContratoServidorPadrao->recuperaRelacionamento( $rsPadrao,$stFiltro,"",$boTransacao );
            $arDadosSalario['padrao_salarial'] = $rsPadrao->getCampo('descricao');
            $this->obRPessoalContratoServidor->listarContratoServidorInicioProgressao($rsInicioProgressao);
            $arDadosSalario['dt_inicio_progressao'] = $rsInicioProgressao->getCampo('dt_inicio_progressao');
                
            $rsProgressao = new Recordset;
            $this->obRPessoalContratoServidor->listarContratoServidorNivelPadrao($rsNivelPadrao);
            if ( $rsNivelPadrao->getNumLinhas() > 0 ) {
                $stFiltro  = " AND pp.cod_contrato = ".$rsRecordSet->getCampo('cod_contrato');
                $stFiltro .= " AND pp.cod_nivel_padrao = ".$rsNivelPadrao->getCampo('cod_nivel_padrao');
                $obTPessoalContratoServidorNivelPadrao =  new TPessoalContratoServidorNivelPadrao;
                $obTPessoalContratoServidorNivelPadrao->recuperaRelacionamento( $rsProgressao,$stFiltro,"",$boTransacao );
            }
            $arDadosSalario['progressao'] = $rsProgressao->getCampo('descricao');
            
            $arDadosSalario['salario']    = $rsSalario->getCampo('salario');
            
            $rsTipoPagamento = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalTipoPagamento->recuperaTodosTipoPagamento( $rsTipoPagamento, " WHERE cod_tipo_pagamento =".$rsRecordSet->getCampo('cod_tipo_pagamento') );
            $arDadosSalario['tipo_pagamento'] = $rsTipoPagamento->getCampo('descricao');
        }

        //Gera Recordset com as informações bancarias do servidor
        if ( isset($arFiltro['boBancarias']) ) {

            $rsFormaPagamento = new Recordset;
            $this->obRPessoalContratoServidor->obRPessoalFormaPagamento->listarFormaPagamento( $rsFormaPagamento, " WHERE cod_forma_pagamento =".$rsRecordSet->getCampo('cod_forma_pagamento') );
            $arDadosBancarios['forma_pagamento'] = $rsFormaPagamento->getCampo('descricao');
                
            $rsBanco = new Recordset;
            $this->obRPessoalContratoServidor->listarContratoServidorContaSalario($rsContaSalario);
            $this->obRPessoalContratoServidor->obRMonetarioBancoFGTS->obTMONBanco->setDado("cod_banco", $rsContaSalario->getCampo('cod_banco') );
            $this->obRPessoalContratoServidor->obRMonetarioBancoFGTS->obTMONBanco->recuperaPorChave( $rsBanco );
            $arDadosBancarios['banco'] = $rsBanco->getCampo('num_banco').' - '.$rsBanco->getCampo('nom_banco');
            
            $rsAgencia = new Recordset;
            $this->obRPessoalContratoServidor->obRMonetarioAgenciaFGTS->obTMONAgencia->setDado("cod_agencia", $rsContaSalario->getCampo('cod_agencia') );
            $this->obRPessoalContratoServidor->obRMonetarioAgenciaFGTS->obTMONAgencia->recuperaPorChave( $rsAgencia );
            $arDadosBancarios['agencia']   = $rsAgencia->getCampo('num_agencia').' - '.$rsAgencia->getCampo('nom_agencia');
            $arDadosBancarios['num_conta'] = $rsContaSalario->getCampo('nr_conta');
        }

        //Geração do Recordset com dados da lotacao do servidor    
        if ( isset($arFiltro['boLotacao']) || isset($arFiltro['boLocal']) ) {
            $arDadosLotacao['lotacao'] = $rsRecordSet->getCampo('lotacao');
            $arDadosLotacao['local']   = $rsRecordSet->getCampo('local');
        }
        
        //Geração do Recordset com dados da previdência do servidor
        if ( isset($arFiltro['boPrevidencia']) ) {
            //$arRegimePrevidencia = array();
            $rsServidorPrevidencia = new Recordset;
            $obTPessoalContratoServidorPrevidencia = new TPessoalContratoServidorPrevidencia;
            $stFiltro = " AND contrato_servidor_previdencia.cod_contrato = ".$rsRecordSet->getCampo('cod_contrato');
            $obTPessoalContratoServidorPrevidencia->recuperaRelacionamento( $rsServidorPrevidencia, $stFiltro, $stOrdem, $boTransacao );
            
            if ($rsServidorPrevidencia->getNumLinhas() > 0) {
                $this->obRFolhaPagamentoPrevidencia->setCodPrevidencia( $rsServidorPrevidencia->getCampo('cod_previdencia') );
                $this->obRFolhaPagamentoPrevidencia->consultarPrevidencia();
                //$arRegimePrevidencia['cod_contrato'] = $dado['cod_contrato'];
                //$arRegimePrevidencia['cod_previdencia'] = $this->obRFolhaPagamentoPrevidencia->getCodPrevidencia();
                //$arRegimePrevidencia['descricao'] = $this->obRFolhaPagamentoPrevidencia->getDescricao();
            }
            $arDadosPrevidencia['regime_previdencia'] = $this->obRFolhaPagamentoPrevidencia->getDescricao();
        }
        

        if ( isset($arFiltro['boFerias']) ) {

            $stTipoFiltro = $arFiltro['stTipoFiltro'];

            if ($arFiltro['stTipoFiltro'] == 'lotacao' || $arFiltro['stTipoFiltro'] == 'lotacao_grupo') {
                $stTipoFiltro = 'contrato';
            }
            
            include_once CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php";
            $obTPessoalFerias = new TPessoalFerias();
            switch ($arFiltro['stTipoFiltro']) {
                case "contrato":
                case "contrato_todos":
                case "contrato_rescisao":
                case "contrato_aposentado":
                case "contrato_pensionista":
                case "cgm_contrato":
                case "cgm_contrato_todos":
                case "cgm_contrato_rescisao":
                case "cgm_contrato_aposentado":
                case "cgm_contrato_pensionista":        
                case "lotacao":
                case "lotacao_grupo":
                    $stValoresFiltro = $rsRecordSet->getCampo('cod_contrato');
                break;
                case "local_grupo":
                case "local":
                    $stValoresFiltro = $rsRecordSet->getCampo('cod_local');
                break;
                case 'atributo_servidor':
                case 'atributo':
                    $rsAtributos = new Recordset;
                    $this->obRPessoalContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array("cod_contrato"=>$rsRecordSet->getCampo('cod_contrato')) );
                    $this->obRPessoalContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
                    
                    foreach ($rsAtributos->getElementos() as $key => $atributo) {
                        if (($atributo['cod_atributo'] == $arFiltro['inCodAtributo']) && ($atributo['cod_cadastro'] == $arFiltro['inCodCadastro'])) {
                            $arDadosAtributos[$key]['atributo'] = $atributo['nom_atributo'].": ".$atributo['valor'];
                            $stValoresFiltro = $atributo['cod_cadastro']."#".$atributo['cod_atributo']."#".$atributo['valor']."";
                        }
                    }
                break;
            }
 
            $obTPessoalFerias->setDado('cod_entidade', Sessao::getEntidade()     );
            $obTPessoalFerias->setDado('exercicio'   , Sessao::getExercicio()    );
            $obTPessoalFerias->setDado('data_limite' , date('d/m/Y')             );
            # Busca sempre por contrato, pois as férias estão ligadas a um.
            $obTPessoalFerias->setDado('tipo_filtro' , $stTipoFiltro );
            $obTPessoalFerias->setDado('valor_filtro', $stValoresFiltro          );
            $obTPessoalFerias->recuperaHistoricoFeriasRelatorio($rsHistoricoFerias, "", "", $boTransacao);

            foreach ($rsHistoricoFerias->getElementos() as $key => $ferias) {
                if ($ferias['dt_inicial_gozo'] != '' && $ferias['dt_final_gozo'] != '') {
                    $arDadosFerias[$key]['nro']                = $key + 1; 
                    $arDadosFerias[$key]['periodo_aquisitivo'] = $ferias['dt_inicial_aquisitivo']." a ".$ferias['dt_final_aquisitivo'];
                    $arDadosFerias[$key]['periodo_gozo']       = $ferias['dt_inicial_gozo']." a ".$ferias['dt_final_gozo'];
                    $arDadosFerias[$key]['faltas']             = $ferias['faltas'];
                    $arDadosFerias[$key]['ferias']             = $ferias['ferias'];
                    $arDadosFerias[$key]['abono']              = $ferias['abono'];
                    $arDadosFerias[$key]['mes_pagamento']      = $ferias['mes_pagamento'];
                    $arDadosFerias[$key]['folha']              = $ferias['folha'];
                    $arDadosFerias[$key]['somente_13']         = $ferias['pagar_13'];
                }
            }
        }

        //Gera Recordset com as informações dos atributos dinâmicos
        if ( isset($arFiltro['boAtributos']) ) {

            $rsAtributos = new Recordset;
            $this->obRPessoalContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array("cod_contrato"=>$rsRecordSet->getCampo('cod_contrato')) );
            $this->obRPessoalContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
            
            foreach ($rsAtributos->getElementos() as $key => $atributo) {
                #if (($atributo['cod_atributo'] == $arFiltro['inCodAtributo']) && ($atributo['cod_cadastro'] == $arFiltro['inCodCadastro'])) {
                    $arDadosAtributos[$key]['atributo'] = $atributo['nom_atributo'].": ".$atributo['valor'];
                    $arAtributoAssentamento = $atributo['cod_cadastro']."#".$atributo['cod_atributo']."#".$atributo['valor']."";
                #}
            }
        
        }
        
        //Geração do Recordset com dados dos dependentes do servidor
        if ( isset($arFiltro['boDependentes']) ) {
            
            $rsDependentes = new Recordset;
            $this->obRPessoalServidor->setCodServidor( $rsRecordSet->getCampo('cod_servidor') );
            $this->obRPessoalServidor->addDependente();
            $this->obRPessoalServidor->roUltimoDependente->listarPessoalDependente( $rsDependentes );
           
            foreach ($rsDependentes->getElementos() as $key => $dependentes) {
                
                $arDadosDependentes[$key]['nome']           = $dependentes['nom_cgm'];
                $arDadosDependentes[$key]['dt_nascimento']  = $dependentes['dt_nascimento'];
                $arDadosDependentes[$key]['cid_dependente'] = $dependentes['descricao_cid'];

                $rsGrauParentesto = new Recordset;
                $this->obRPessoalServidor->roUltimoDependente->obRPessoalGrauParentesco->obTGrauParentesco->setDado('cod_grau',$dependentes['cod_grau']);
                $this->obRPessoalServidor->roUltimoDependente->obRPessoalGrauParentesco->obTGrauParentesco->recuperaPorChave( $rsGrauParentesco );
                
                $arDadosDependentes[$key]['grau_parentesco'] = $rsGrauParentesco->getCampo('nom_grau');
                //$arDadosDependentes[$key]['grau_parentesco'] = $dependentes['descricao_vinculo'];
                    
                if ( $dependentes['sexo'] == 'F' ) {
                    $arDadosDependentes[$key]['sexo'] = "Feminino";
                } else {
                    $arDadosDependentes[$key]['sexo'] = "Masculino";
                }
                    
                $arDadosDependentes[$key]['escolaridade'] = $dependentes['escolaridade'];
                
                if ( $dependentes['dependente_sal_familia'] == 't' ) {
                    $arDadosDependentes[$key]['dependente_sal_familia'] = "Sim";
                } else {
                    $arDadosDependentes[$key]['dependente_sal_familia'] = "Não";
                }
                    
                $arDataNascimentoDependente = explode( "/", $rsDependentes->getCampo('dt_nascimento') );
                include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSalarioFamilia.class.php");
                $obRFolhaPagamentoSalarioFamilia = new RFolhaPagamentoSalarioFamilia;
                $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodPrevidencia( $inCodPrevidenciaOficioal );
                $obRFolhaPagamentoSalarioFamilia->listarSalarioFamilia( $rsSalariosFamilia );
                $stAnoLimte = $arDataNascimentoDependente[2] + $rsSalariosFamilia->getCampo("idade_limite");
                $stDataLimiteSalarioFamilia = $arDataNascimentoDependente[0] ."/". $arDataNascimentoDependente[1] ."/". $stAnoLimte;
                $arDadosDependentes[$key]['limite_salario_familia'] = $stDataLimiteSalarioFamilia;
                    
                //"Dependente IR";
                $arDadosDependentes[$key]['dependencia_irrf'] = $dependentes['descricao_vinculo'];
            }
        }
        
        if ( isset($arFiltro['boAssentamentos']) ) {

            $stTipoFiltro = $arFiltro['stTipoFiltro'];

            if ($arFiltro['stTipoFiltro'] == 'lotacao') {
                $stTipoFiltro = 'contrato';
            }

            include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamento.class.php";
            $obTPessoalAssentamento = new TPessoalAssentamento();
            $obTPessoalAssentamento->setDado('cod_entidade', Sessao::getEntidade()                  );
            $obTPessoalAssentamento->setDado('exercicio'   , Sessao::getExercicio()                 );                
            $obTPessoalAssentamento->setDado('tipo_filtro' , $stTipoFiltro );

            switch ($arFiltro['stTipoFiltro']) {
                case 'lotacao':
                case 'contrato':
                case 'contrato_rescisao':
                case 'contrato_aposentado':
                case 'cgm_contrato':
                    $obTPessoalAssentamento->setDado('dado_filtro', $rsRecordSet->getCampo('cod_contrato') );
                break;
                case 'local':
                    $obTPessoalAssentamento->setDado('dado_filtro', $rsRecordSet->getCampo('cod_local') );
                break;
                case 'atributo_servidor':
                    $obTPessoalAssentamento->setDado('dado_filtro', (string)$arAtributoAssentamento );
                break;
            }
            
            $obTPessoalAssentamento->setDado('cod_contrato', $rsRecordSet->getCampo('cod_contrato') );                
            $obTPessoalAssentamento->recuperaAssentamentoRelatorio($rsAssentamentos, "", "ORDER BY classificacao, periodo_inicial, nom_cgm", $boTransacao);

            foreach ($rsAssentamentos->getElementos() as $key => $assentamentos) {
                $arDadosAssentamentos[$key]['classificacao'] = $assentamentos['classificacao'];
                $arDadosAssentamentos[$key]['assentamento']  = $assentamentos['assentamento'];
                $arDadosAssentamentos[$key]['periodo']       = $assentamentos['periodo'];
                $arDadosAssentamentos[$key]['dias']          = $assentamentos['dias'];
                $arDadosAssentamentos[$key]['norma']         = $assentamentos['norma'];
                $arDadosAssentamentos[$key]['observacao']    = $assentamentos['observacao'];
            }
        }

        //SETANDO OS ARRAYS
        $arSpan          = isset($arDadosSpans)         ? $arDadosSpans         : "";
        $arIdentificacao = isset($arDadosIdentificacao) ? $arDadosIdentificacao : "";
        $arDocumentacao  = isset($arDadosDocumentacao)  ? $arDadosDocumentacao  : "";
        $arContratuais   = isset($arDadosContratuais)   ? $arDadosContratuais   : "";
        $arSalariais     = isset($arDadosSalario)       ? $arDadosSalario       : "";
        $arBancarios     = isset($arDadosBancarios)     ? $arDadosBancarios     : "";
        $arLotacao       = isset($arDadosLotacao)       ? $arDadosLotacao       : "";
        $arFerias        = isset($arDadosFerias)        ? $arDadosFerias        : "";
        $arAtributos     = isset($arDadosAtributos)     ? $arDadosAtributos     : "";
        $arDependentes   = isset($arDadosDependentes)   ? $arDadosDependentes   : "";
        $arAssentamento  = isset($arDadosAssentamentos) ? $arDadosAssentamentos : "";
        $arPrevidencia   = isset($arDadosPrevidencia)   ? $arDadosPrevidencia   : "";


        //ATRIBUINDO DADOS PARA SALVAR NO RECORD SET
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_titulo']        = $arDadosTitulo;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_spans']         = $arDadosSpans;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_identificacao'] = $arIdentificacao;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_documentacao']  = $arDocumentacao;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_contratuais']   = $arContratuais;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_salariais']     = $arSalariais;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_bancarios']     = $arBancarios;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_lotacao']       = $arLotacao;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_previdencia']   = $arPrevidencia;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_ferias']        = $arFerias;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_atributos']     = $arAtributos;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_dependentes']   = $arDependentes;
        $arDadosRelatorio['servidores'][$rsRecordSet->getCorrente()]['dados_assentamento']  = $arAssentamento;
        

        //UNSET TODOS OS ARRAYS
        unset($arDadosTitulo);
        unset($arDadosSpans);
        unset($arDadosIdentificacao);
        unset($arDadosDocumentacao);
        unset($arDadosContratuais);
        unset($arDadosSalario);
        unset($arDadosBancarios);
        unset($arDadosLotacao);
        unset($arDadosPrevidencia);
        unset($arDadosFerias);
        unset($arDadosAtributos);
        unset($arDadosDependentes);
        unset($arDadosAssentamentos);
        
        $rsRecordSet->proximo();

    }
    
    $rsRecordset = new RecordSet();
    $rsRecordset->preenche( $arDadosRelatorio );
    
    return $obErro;
}

}
