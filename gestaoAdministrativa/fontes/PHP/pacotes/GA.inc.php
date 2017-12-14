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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.00.00
*/

//DEFINICAO DE VALIDADE E VERSAO DA GESTAO
define( "VALIDADE_GA",               "31/12/2017" );
define( "VERSAO_GA",                 "2.05.4" );

define( "CAM_GA", "../../../../../../gestaoAdministrativa/fontes/" );

//DEFINICAO DOS CAMINHOS

define( "CAM_ADMINISTRACAO",         CAM_GA."PHP/administracao/" );
define( "CAM_CGM",                   CAM_GA."PHP/CGM/"           );
define( "CAM_CSE",                   CAM_GA."PHP/CSE/"           );
define( "CAM_FPDF",                  CAM_GA."PHP/FPDF/"          );
define( "CAM_AGATA",                 CAM_GA."PHP/agata/"         );
define( "CAM_FRAMEWORK",             CAM_GA."PHP/framework/"     );
define( "CAM_NORMAS",                CAM_GA."PHP/normas/"        );
define( "CAM_ORGANOGRAMA",           CAM_GA."PHP/organograma/"   );
define( "CAM_PROTOCOLO",             CAM_GA."PHP/protocolo/"     );
define( "CAM_OOPARSER",              CAM_GA."PHP/TinyButStrong/" );

//ADMINISTRACAO
define( "CAM_GA_ADM_CLASSES",           CAM_ADMINISTRACAO."classes/"        );
define( "CAM_GA_ADM_MAPEAMENTO",        CAM_GA_ADM_CLASSES."mapeamento/"    );
define( "CAM_GA_ADM_NEGOCIO",           CAM_GA_ADM_CLASSES."negocio/"       );
define( "CAM_GA_ADM_FUNCAO",            CAM_GA_ADM_CLASSES."funcao/"        );
define( "CAM_GA_ADM_INSTANCIAS",        CAM_ADMINISTRACAO."instancias/"     );
define( "CAM_GA_ADM_PROCESSAMENTO",     CAM_GA_ADM_INSTANCIAS."processamento/"     );
define( "CAM_GA_ADM_COMPONENTES",       CAM_GA_ADM_CLASSES."componentes/"   );
define( "CAM_GA_ADM_POPUPS",            CAM_ADMINISTRACAO."popups/"         );
define( "CAM_GA_ADM_ANEXOS",            CAM_ADMINISTRACAO."anexos/"         );
define( "TADM",                         CAM_GA_ADM_MAPEAMENTO               );

//CGM
define( "CAM_GA_CGM_CLASSES",           CAM_CGM."classes/"                  );
define( "CAM_GA_CGM_MAPEAMENTO",        CAM_GA_CGM_CLASSES."mapeamento/"    );
define( "CAM_GA_CGM_NEGOCIO",           CAM_GA_CGM_CLASSES."negocio/"       );
define( "CAM_GA_CGM_INSTANCIAS",        CAM_CGM."instancias/"               );
define( "CAM_GA_CGM_COMPONENTES",       CAM_GA_CGM_CLASSES."componentes/"   );
define( "CAM_GA_CGM_PROCESSAMENTO",     CAM_GA_CGM_INSTANCIAS."processamento/"     );
define( "CAM_GA_CGM_POPUPS",            CAM_CGM."popups/"                   );
define( "TCGM",                         CAM_GA_CGM_MAPEAMENTO               );

//CSE
define( "CAM_GA_CSE_CLASSES",           CAM_CSE."classes/"              );
define( "CAM_GA_CSE_MAPEAMENTO",        CAM_GA_CSE_CLASSES."mapeamento/");
define( "CAM_GA_CSE_NEGOCIO",           CAM_GA_CSE_CLASSES."negocio/"   );
define( "CAM_GA_CSE_INSTANCIAS",        CAM_CSE."instancias/"           );
define( "CAM_GA_CSE_POPUPS",            CAM_CSE."popups/"               );
define( "TCSE",                         CAM_GA_CSE_MAPEAMENTO           );

//FRAMEWORK
define( "CAM_FW_ARQUIVOS",           CAM_FRAMEWORK."arquivos/"              );
define( "CAM_FW_BANCO_DADOS",        CAM_FRAMEWORK."bancoDados/postgreSQL/" );
define( "CAM_FW_COMPONENTES",        CAM_FRAMEWORK."componentes/"           );
define( "CAM_FW_HTML",               CAM_FW_COMPONENTES."HTML/"             );
define( "CAM_FW_PDF",                CAM_FW_COMPONENTES."PDF/"              );
define( "CAM_FW_MASCARA",            CAM_FW_COMPONENTES."mascara/"          );
define( "CAM_FW_EXCECAO",            CAM_FRAMEWORK."excecao/"               );
define( "CAM_FW_EXPORTACAO",         CAM_FRAMEWORK."exportacao/"            );
define( "CAM_FW_IMPORTACAO",         CAM_FRAMEWORK."importacao/"            );
define( "CAM_FW_XML",                CAM_FRAMEWORK."xml/"                   );
define( "CAM_FW_OBJETO",             CAM_FRAMEWORK."objeto/"                );
define( "CAM_FW_PACOTES",            CAM_FRAMEWORK."pacotes/"               );
define( "CAM_FW_URBEM",              CAM_FRAMEWORK."URBEM/"                 );
define( "CAM_FW_TEMAS",              CAM_FRAMEWORK."temas/padrao/"          );
define( "CAM_FW_CSS",                CAM_FW_TEMAS."CSS/"                    );
define( "CAM_FW_IMAGENS",            CAM_FW_TEMAS."imagens/"                );
define( "CAM_FW_INCLUDE",            CAM_FRAMEWORK."include/"               );
define( "CAM_FW_TIPO",               CAM_FRAMEWORK."tipo/"                  );
define( "CAM_FW_POPUPS",             CAM_FRAMEWORK."popups/"                );
define( "CAM_FW_INSTANCIAS",         CAM_FRAMEWORK."instancias/"            );
define( "CAM_FW_LEGADO",             CAM_FRAMEWORK."legado/"                );
define( "CAM_FW_APPLETS",            CAM_FRAMEWORK."applets/"               );
define( "CAM_FW_BIRT",    	         CAM_FRAMEWORK."birt/"                  );
define( "CAM_FW_MPDF",               CAM_FRAMEWORK."mpdf/"                  );
define( "CAM_FW_TMP",                CAM_FRAMEWORK."tmp/"                   );

//NORMAS
define( "CAM_GA_NORMAS_CLASSES",        CAM_NORMAS."classes/"               );
define( "CAM_GA_NORMAS_MAPEAMENTO",     CAM_GA_NORMAS_CLASSES."mapeamento/" );
define( "CAM_GA_NORMAS_COMPONENTES",    CAM_GA_NORMAS_CLASSES."componentes/");
define( "CAM_GA_NORMAS_NEGOCIO",        CAM_GA_NORMAS_CLASSES."negocio/"    );
define( "CAM_GA_NORMAS_INSTANCIAS",     CAM_NORMAS."instancias/"            );
define( "CAM_GA_NORMAS_PROCESSAMENTO",  CAM_NORMAS."instancias/processamento/");
define( "CAM_GA_NORMAS_POPUPS",         CAM_NORMAS."popups/"                );
define( "TNOR",                         CAM_GA_NORMAS_MAPEAMENTO            );

//ORGANOGRAMAGA_
define( "CAM_GA_ORGAN_CLASSES",         CAM_ORGANOGRAMA."classes/"          );
define( "CAM_GA_ORGAN_MAPEAMENTO",      CAM_GA_ORGAN_CLASSES."mapeamento/"  );
define( "CAM_GA_ORGAN_NEGOCIO",         CAM_GA_ORGAN_CLASSES."negocio/"     );
define( "CAM_GA_ORGAN_COMPONENTES",     CAM_GA_ORGAN_CLASSES."componentes/" );
define( "CAM_GA_ORGAN_INSTANCIAS",      CAM_ORGANOGRAMA."instancias/"       );
define( "CAM_GA_ORGAN_POPUPS",          CAM_ORGANOGRAMA."popups/"           );
define( "TORG",                         CAM_GA_ORGAN_MAPEAMENTO             );

//PROTOCOLO
define( "CAM_GA_PROT_CLASSES",         CAM_PROTOCOLO."classes/"             );
define( "CAM_GA_PROT_COMPONENTES",     CAM_GA_PROT_CLASSES."componentes/"   );
define( "CAM_GA_PROT_MAPEAMENTO",      CAM_GA_PROT_CLASSES."mapeamento/"    );
define( "CAM_GA_PROT_NEGOCIO",         CAM_GA_PROT_CLASSES."negocio/"       );
define( "CAM_GA_PROT_INSTANCIAS",      CAM_PROTOCOLO."instancias/"          );
define( "CAM_GA_PROT_POPUPS",          CAM_PROTOCOLO."popups/"              );
define( "TPRO",                        CAM_GA_PROT_MAPEAMENTO               );

//DEFINICAO DO ALIAS PARA OS ARQUIVOS DE CLASSE
define  ( "CLA_ARQUIVO",                            CAM_FW_ARQUIVOS."Arquivo.class.php"      );
define  ( "CLA_ARQUIVO_CSV",                        CAM_FW_ARQUIVOS."ArquivoCSV.class.php"      );
define  ( "CLA_ARQUIVO_TEXTO",                      CAM_FW_ARQUIVOS."ArquivoTexto.class.php" );
define  ( "CLA_ARQUIVO_ZIP",                        CAM_FW_ARQUIVOS."ArquivoZip.class.php"   );
define  ( "CLA_ZIP_FILE",                           CAM_FW_ARQUIVOS."zipfile.class.php");
define  ( "CLA_LINHA_ARQUIVO",                      CAM_FW_ARQUIVOS."LinhaArquivo.class.php" );

define  ( "CLA_AUDITORIA",                          CAM_FW_BANCO_DADOS."Auditoria.class.php"                   );
define  ( "CLA_CONEXAO",                            CAM_FW_BANCO_DADOS."Conexao.class.php"                     );
define  ( "CLA_CONEXAO_SIAM",                       CAM_FW_BANCO_DADOS."ConexaoSIAM.class.php"                 );
define  ( "CLA_CONEXAO_DBLINK",                     CAM_FW_BANCO_DADOS."ConexaoDBLink.class.php"               );
define  ( "CLA_PERSISTENTE",                        CAM_FW_BANCO_DADOS."Persistente.class.php"                 );
define  ( "CLA_PERSISTENTE_SIAM",                   CAM_FW_BANCO_DADOS."PersistenteSIAM.class.php"             );
define  ( "CLA_PERSISTENTE_ATRIBUTOS",              CAM_FW_BANCO_DADOS."PersistenteAtributos.class.php"        );
define  ( "CLA_PERSISTENTE_ATRIBUTOS_VALORES",      CAM_FW_BANCO_DADOS."PersistenteAtributosValores.class.php" );
//TESTE
define  ( "CLA_ADM_ATRIBUTO_DINAMICO",              CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );
define  ( "CLA_ADM_CADASTRO",                       CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php" );

define  ( "CLA_PERSISTENTE_RELATORIO",              CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php"        );
define  ( "CLA_CAMPOTABELA",                        CAM_FW_BANCO_DADOS."CampoTabela.class.php"                 );
define  ( "CLA_RECORDSET",                          CAM_FW_BANCO_DADOS."RecordSet.class.php"                   );
define  ( "CLA_TRANSACAO",                          CAM_FW_BANCO_DADOS."Transacao.class.php"                   );
define  ( "CLA_TRANSACAO_SIAM",                     CAM_FW_BANCO_DADOS."TransacaoSIAM.class.php"               );

define  ( "CLA_ARVORE",                             CAM_FW_HTML."Arvore.class.php"          );
define  ( "CLA_GERENCIA_SELECTS",                   CAM_FW_HTML."GerenciaSelects.class.php" );
define  ( "CLA_SELECT_MULTIPLO",                    CAM_FW_HTML."SelectMultiplo.class.php"  );
define  ( "CLA_BUTTON",                             CAM_FW_HTML."Button.class.php"          );
define  ( "CLA_CALENDARIO",                         CAM_FW_HTML."Calendario.class.php"      );
define  ( "CLA_CHECKBOX",                           CAM_FW_HTML."CheckBox.class.php"        );
define  ( "CLA_CHECKBOX_DINAMICO",                  CAM_FW_HTML."CheckBoxDinamico.class.php" );
define  ( "CLA_COMPONENTE",                         CAM_FW_HTML."Componente.class.php"      );
define  ( "CLA_PORCENTAGEM",                        CAM_FW_HTML."Porcentagem.class.php"     );
define  ( "CLA_COMPONENTE_BASE",                    CAM_FW_HTML."ComponenteBase.class.php"  );
define  ( "CLA_LABEL",                              CAM_FW_HTML."Label.class.php"           );
define  ( "CLA_FORM",                               CAM_FW_HTML."Form.class.php"            );
define  ( "CLA_FILEBOX",                            CAM_FW_HTML."FileBox.class.php"         );
define  ( "CLA_HIDDEN",                             CAM_FW_HTML."Hidden.class.php"          );
define  ( "CLA_IMG",                                CAM_FW_HTML."Img.class.php"             );
define  ( "CLA_LINK",                               CAM_FW_HTML."Link.class.php"            );
define  ( "CLA_OPTION",                             CAM_FW_HTML."Option.class.php"          );
define  ( "CLA_PASSWORD",                           CAM_FW_HTML."PassWord.class.php"        );
define  ( "CLA_PROGRESSBAR",                        CAM_FW_HTML."ProgressBar.class.php"     );
define  ( "CLA_RADIO",                              CAM_FW_HTML."Radio.class.php"           );
define  ( "CLA_RESET",                              CAM_FW_HTML."Reset.class.php"           );
define  ( "CLA_SELECT",                             CAM_FW_HTML."Select.class.php"          );
define  ( "CLA_SUBMIT",                             CAM_FW_HTML."Submit.class.php"          );
define  ( "CLA_SPAN",                               CAM_FW_HTML."Span.class.php"            );
define  ( "CLA_TEXTAREA",                           CAM_FW_HTML."TextArea.class.php"        );
define  ( "CLA_TEXTBOX",                            CAM_FW_HTML."TextBox.class.php"         );
define  ( "CLA_TEXTBOX_SELECT",                     CAM_FW_HTML."TextBoxSelect.class.php"   );
define  ( "CLA_APPLET",                             CAM_FW_HTML."Applet.class.php"          );

define  ( "CLA_BUSCA",                              CAM_FW_HTML."Busca.class.php"                );
define  ( "CLA_BUSCAINNER",                         CAM_FW_HTML."BuscaInner.class.php"           );
define  ( "CLA_POPUP",                              CAM_FW_HTML."PopUp.class.php"                );
define  ("CLA_BUSCAINNERINTERVALO",                 CAM_FW_HTML."BuscaInnerIntervalo.class.php"  );
define  ( "CLA_CAMPOINNER",                         CAM_FW_HTML."CampoInner.class.php"           );
define  ( "CLA_CANCELAR",                           CAM_FW_HTML."Cancelar.class.php"             );
define  ( "CLA_CEP",                                CAM_FW_HTML."CEP.class.php"                  );
define  ( "CLA_CNPJ",                               CAM_FW_HTML."CNPJ.class.php"                 );
define  ( "CLA_CPF",                                CAM_FW_HTML."CPF.class.php"                  );
define  ( "CLA_DADO_COMPONENTE",                    CAM_FW_HTML."DadoComponente.class.php"       );
define  ( "CLA_DADO_TEXTBOX",                       CAM_FW_HTML."DadoTextBox.class.php"          );
define  ( "CLA_DATA",                               CAM_FW_HTML."Data.class.php"                 );
define  ( "CLA_PLACA_VEICULO",                      CAM_FW_HTML."PlacaVeiculo.class.php"         );
define  ( "CLA_PERIODO",                            CAM_FW_HTML."Periodo.class.php"              );
define  ( "CLA_PERIODICIDADE",                      CAM_FW_HTML."Periodicidade.class.php"        );
define  ( "CLA_MES",                                CAM_FW_HTML."Mes.class.php"                  );
define  ( "CLA_EXERCICIO",                          CAM_FW_HTML."Exercicio.class.php"            );
define  ( "CLA_QUANTIDADE",                         CAM_FW_HTML."Quantidade.class.php"           );
define  ( "CLA_VALOR_TOTAL",                        CAM_FW_HTML."ValorTotal.class.php"           );
define  ( "CLA_VALOR_UNITARIO",                     CAM_FW_HTML."ValorUnitario.class.php"        );
define  ( "CLA_HIDDENEVAL",                         CAM_FW_HTML."HiddenEval.class.php"           );
define  ( "CLA_HORA",                               CAM_FW_HTML."Hora.class.php"                 );
define  ( "CLA_LIMPAR",                             CAM_FW_HTML."Limpar.class.php"               );
define  ( "CLA_OK",                                 CAM_FW_HTML."Ok.class.php"                   );
define  ( "CLA_SELECT_MESES",                       CAM_FW_HTML."SelectMeses.class.php"          );
define  ( "CLA_BIMESTRE",                           CAM_FW_HTML."Bimestre.class.php"             );
define  ( "CLA_SIMNAO",                             CAM_FW_HTML."SimNao.class.php"               );
define  ( "CLA_MOEDA",                              CAM_FW_HTML."Moeda.class.php"                );
define  ( "CLA_NUMERICO",                           CAM_FW_HTML."Numerico.class.php"             );
define  ( "CLA_INTEIRO",                            CAM_FW_HTML."Inteiro.class.php"              );
define  ( "CLA_VOLTAR",                             CAM_FW_HTML."Voltar.class.php"               );
define  ( "CLA_IFRAME",                             CAM_FW_HTML."IFrame.class.php"               );
define  ( "CLA_TIPO_BUSCA",                         CAM_FW_HTML."TipoBusca.class.php"            );
define  ( "CLA_POPUPEDIT",                          CAM_FW_HTML."PopUpEdit.class.php"            );

define  ( "CLA_ABA",                                CAM_FW_HTML."Aba.class.php"             );
define  ( "CLA_ACAO",                               CAM_FW_HTML."Acao.class.php"            );
define  ( "CLA_CABECALHO",                          CAM_FW_HTML."Cabecalho.class.php"       );
define  ( "CLA_CELULA",                             CAM_FW_HTML."Celula.class.php"          );
define  ( "CLA_DADO",                               CAM_FW_HTML."Dado.class.php"            );
define  ( "CLA_DATAGRID",                           CAM_FW_HTML."DataGrid.class.php"        );
define  ( "CLA_EVENTO",                             CAM_FW_HTML."Evento.class.php"          );
define  ( "CLA_FORMULARIO",                         CAM_FW_HTML."Formulario.class.php"      );
define  ( "CLA_FORMULARIO_ABAS",                    CAM_FW_HTML."FormularioAbas.class.php"  );
define  ( "CLA_INNER_FORMULARIO",                   CAM_FW_HTML."InnerFormulario.class.php" );
define  ( "CLA_JAVASCRIPT",                         CAM_FW_HTML."JavaScript.class.php"      );
define  ( "CLA_LINHA",                              CAM_FW_HTML."Linha.class.php"           );
define  ( "CLA_LISTA",                              CAM_FW_HTML."Lista.class.php"           );
define  ( "CLA_PAGINACAO",                          CAM_FW_HTML."Paginacao.class.php"       );
define  ( "CLA_TABELA",                             CAM_FW_HTML."Tabela.class.php"          );
define  ( "CLA_MONTA_ATRIBUTOS",                    CAM_FW_HTML."MontaAtributos.class.php"  );
define  ( "CLA_ORDENACAO",                          CAM_FW_HTML."Ordenacao.class.php"       );
define  ( "CLA_IMAGE_BOX",                          CAM_FW_HTML."ImageBox.class.php"        );

define  ( "CLA_ERRO",                               CAM_FW_EXCECAO."Erro.class.php" );
define  ( "CLA_EXCECAO",                            CAM_FW_EXCECAO."Excecao.class.php" );

define  ( "CLA_EXPORTADOR",                         CAM_FW_EXPORTACAO."Exportador.class.php"                   );
define  ( "CLA_ARQUIVO_EXPORTADOR",                 CAM_FW_EXPORTACAO."ArquivoExportador.class.php"            );
define  ( "CLA_ARQUIVO_EXPORTADOR_BLOCO",           CAM_FW_EXPORTACAO."ArquivoExportadorBloco.class.php"       );
define  ( "CLA_ARQUIVO_EXPORTADOR_BLOCO_COLUNA",    CAM_FW_EXPORTACAO."ArquivoExportadorBlocoColuna.class.php" );

define  ( "CLA_IMPORTADOR",                         CAM_FW_IMPORTACAO."Importador.class.php"                   );
define  ( "CLA_ARQUIVO_IMPORTADOR",                 CAM_FW_IMPORTACAO."ArquivoImportador.class.php"            );
define  ( "CLA_ARQUIVO_IMPORTADOR_COLUNA",          CAM_FW_IMPORTACAO."ArquivoImportadorColuna.class.php"       );

define  ( "CLA_OBJETO",                             CAM_FW_OBJETO."Objeto.class.php" );

define  ( "CLA_FPDF",                               CAM_FPDF."fpdf.php"                 );
define  ( "CLA_AGATA",                              CAM_AGATA."classes/core/AgataAPI.class" );
define  ( "CLA_LISTA_PDF",                          CAM_FW_PDF."ListaPDF.class.php"     );
define  ( "CLA_LISTA_FORM_PDF",                     CAM_FW_PDF."ListaFormPDF.class.php" );
define  ( "CLA_DOCUMENTO_PDF",                      CAM_FW_PDF."DocumentoPDF.class.php" );
define  ( "CLA_DOCUMENTO_DINAMICO",                 CAM_FW_PDF."DocumentoDinamico.class.php" );

define  ( "CLA_MPDF",                               CAM_FW_MPDF."FrameWorkMPDF.class.php");
define  ( "CLA_LISTA_MPDF",                         CAM_FW_PDF."ListaMPDF.class.php"     );

define  ( "CLA_MASCARA",                            CAM_FW_MASCARA."Mascara.class.php"     );
define  ( "CLA_MASCARA_CEP",                        CAM_FW_MASCARA."MascaraCEP.class.php"  );
define  ( "CLA_MASCARA_CNPJ",                       CAM_FW_MASCARA."MascaraCNPJ.class.php" );
define  ( "CLA_MASCARA_CPF",                        CAM_FW_MASCARA."MascaraCPF.class.php"  );
define  ( "CLA_MASCARA_DATA",                       CAM_FW_MASCARA."MascaraData.class.php" );

define  ( "CLA_SESSAO",                             CAM_FW_URBEM."Sessao.class.php"        );
define  ( "CLA_SESSAO_LEGADA",                      CAM_FW_URBEM."SessaoLegada.class.php"  );
define  ( "CLA_SISTEMA_LEGADO",                     CAM_FW_URBEM."SistemaLegado.class.php" );

//IMAGENS
define  ( "IMG_DESARQUIVAR",          CAM_FW_IMAGENS."botao_desarquiva.png"   );
define  ( "IMG_DESPACHAR",            CAM_FW_IMAGENS."botao_despachar.png"    );
define  ( "IMG_ERRO",                 CAM_FW_IMAGENS."botao_erro.png"         );
define  ( "IMG_POPUP",                CAM_FW_IMAGENS."botao_popup.png"        );
define  ( "IMG_RECEBER",              CAM_FW_IMAGENS."botao_receber.png"      );
define  ( "IMG_EDITAR",               CAM_FW_IMAGENS."btneditar.gif"          );
define  ( "IMG_EDITAR_16PX",          CAM_FW_IMAGENS."btneditar16px.png"      );
define  ( "IMG_EXCLUIR",              CAM_FW_IMAGENS."btnexcluir.gif"         );
define  ( "IMG_REMOVER",              CAM_FW_IMAGENS."btnexcluir.gif"         );
define  ( "IMG_EXCLUIR_16PX",         CAM_FW_IMAGENS."btnexcluir16px.png"     );
define  ( "IMG_INCLUIR",              CAM_FW_IMAGENS."btnincluir.gif"         );
define  ( "IMG_BAIXAR",               CAM_FW_IMAGENS."botao_expandir.png"     );
define  ( "IMG_BAIXAR_15PX",          CAM_FW_IMAGENS."botao_expandir15px.png" );
define  ( "IMG_SUBIR",                CAM_FW_IMAGENS."botao_retrair.png"      );
define  ( "IMG_SUBIR_15PX",           CAM_FW_IMAGENS."botao_retrair15px.png"  );
define  ( "IMG_CONSULTAR",            CAM_FW_IMAGENS."look.gif"               );
define  ( "IMG_RENOMEAR",             CAM_FW_IMAGENS."btnrenomear.png"        );
define  ( "IMG_SELECIONAR",           CAM_FW_IMAGENS."btnselecionar.png"      );
define  ( "IMG_CASSAR",               CAM_FW_IMAGENS."btncassar.png"          );
define  ( "IMG_SUSPENDER",            CAM_FW_IMAGENS."btnsuspender.png"       );
define  ( "IMG_CANCELAR",             CAM_FW_IMAGENS."btncancelar.png"        );
define  ( "IMG_DETALHAR",             CAM_FW_IMAGENS."btndetalhar.png"        );
define  ( "IMG_REFORMA",              CAM_FW_IMAGENS."btnreforma.png"         );
define  ( "IMG_AGLUTINAR",            CAM_FW_IMAGENS."btnaglutinar.png"       );
define  ( "IMG_DESMEMBRAR",           CAM_FW_IMAGENS."btndesmembrar.png"      );
define  ( "IMG_IMOVEL",               CAM_FW_IMAGENS."btnImovel.png"          );
define  ( "IMG_LOTE",                 CAM_FW_IMAGENS."btnLote.png"            );
define  ( "IMG_PROPRIETARIO",         CAM_FW_IMAGENS."btnProprietario.png"    );
define  ( "IMG_RELATORIO",            CAM_FW_IMAGENS."btnRelatorio.png"       );
define  ( "IMG_TRANSFERENCIA",        CAM_FW_IMAGENS."btnTransferencia.png"   );
define  ( "IMG_CONDOMINIO",           CAM_FW_IMAGENS."btnCondominio.png"      );
define  ( "IMG_ALIQUOTA",             CAM_FW_IMAGENS."btnAliquota.png"        );
define  ( "IMG_EMPRESA",              CAM_FW_IMAGENS."btnEmpresa.png"         );
define  ( "IMG_ATIVIDADE",            CAM_FW_IMAGENS."btnAtividade.png"       );
define  ( "IMG_LICENCA",              CAM_FW_IMAGENS."btnLicenca.png"         );
define  ( "IMG_RESCINDIR",            CAM_FW_IMAGENS."btnRescindir.png"       );
define  ( "IMG_RESUMIR",              CAM_FW_IMAGENS."btnResumir.png"         );
define  ( "IMG_FORMULA",              CAM_FW_IMAGENS."btnFormula.png"         );
define  ( "IMG_ATUALIZAR",            CAM_FW_IMAGENS."btnRefresh.png"         );
define  ( "IMG_ABRIR",                CAM_FW_IMAGENS."btnAbrir.png"           );
define  ( "IMG_ANULAR",               CAM_FW_IMAGENS."btnAnular.png"          );
define  ( "IMG_CONVERTER",            CAM_FW_IMAGENS."btnConverter.png"       );
define  ( "IMG_SALVAR",               CAM_FW_IMAGENS."botao_salvar.png"       );
define  ( "IMG_LIMPAR",               CAM_FW_IMAGENS."btnLimparr.png"         );
define  ( "IMG_ESTORNAR",             CAM_FW_IMAGENS."btnEstornar.png"        );
define  ( "IMG_AVANCAR_PROC",         CAM_FW_IMAGENS."btnAvancaProcesso.png"  );
define  ( "IMG_DRAGDROP",             CAM_FW_IMAGENS."btnDragDrop.png"        );
define  ( "IMG_PDF",                  CAM_FW_IMAGENS."btnPDF.png"             );
define  ( "IMG_USUARIO",              CAM_FW_IMAGENS."btnUsuario.png"         );
define  ( "IMG_ATIVOINATIVO",         CAM_FW_IMAGENS."btnAtivoInativo.png"    );
define  ( "IMG_IMPRIMIR",             CAM_FW_IMAGENS."botao_imprimir.png"     );
define  ( "IMG_PUBLICAR",             CAM_FW_IMAGENS."btnPublicar.png"        );
define  ( "IMG_CLASSIFICAR",          CAM_FW_IMAGENS."btnClassificar.png"     );
define  ( "IMG_DESCLASSIFICAR",       CAM_FW_IMAGENS."btnDesclassificar.png"  );
define  ( "IMG_RETIRAR",              CAM_FW_IMAGENS."btnPublicar.png"        );
define  ( "IMG_RETORNAR",             CAM_FW_IMAGENS."btnRetornoProcesso.png" );
define  ( "IMG_CONCEDER",             CAM_FW_IMAGENS."btnConceder.png"        );
define  ( "IMG_PROCESSAR",            CAM_FW_IMAGENS."btnProcessar.png"       );

////FRAMEWORK
//define( "CAM_FW_PACOTES",            CAM_FRAMEWORK."pacotes/" );
//define( "CAM_FW_URBEM",            CAM_FRAMEWORK."URBEM/" );
//define( "CAM_FW_TEMAS",              CAM_FRAMEWORK."temas/padrao/" );
//define( "CAM_FW_CSS",                CAM_FW_TEMAS."CSS/" );
//define( "CAM_FW_IMAGENS",            CAM_FW_TEMAS."imagens/" );
//define( "CAM_FW_TIPO",               CAM_FRAMEWORK."tipo/" );

define( "CLA_IPOPUPCGM",               CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");

//birt
define( "CLA_PREVIEW_BIRT",            CAM_FW_BIRT."classes/PreviewBirt.class.php"         );
?>
